<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('add_biblioitem_record');

if (!empty($_POST['do']) && $_POST['do'] == 'check') {
  lms_check_csrf_token($_POST['csrf_token']);
  $arr = json_decode($_POST['data'],true);
  $res = [];
  foreach ($arr as &$v) {
    if (@$v['BiblioID']) {
      $id = (@$v['BiblioID']=='<<New>>' || !@$v['BiblioID']) ? NULL : $v['BiblioID'];
      $data = array(
        'biblioid'    => $id,
        'title'       => @$v['Title'],
        'callnumber'  => @$v['CallNo'],
        'author'      => @$v['Author'],
        'publisher'   => @$v['Publisher'],
        'isbn'        => @$v['ISBN'],
        'isrestricted'=> @$v['RestrictCheckOut'],
        'dryrun'      => true,
      );
      $v['_res'] = lms_add_biblio_record($data);
      $res[] = $v;
    }
    if (@$v['BiblioItemID']) {
      if (!$res) { continue; }
      $lastBiblio = &$res[array_key_last($res)];
      $thisBiblioItem = [];
      
      if (!!$lastBiblio['_res']['success']) { 
        $id = (@$v['BiblioItemID']=='<<New>>' || !@$v['BiblioItemID']) ? NULL : $v['BiblioItemID'];
        $data = array(
          'biblioitemid'=> $id,
          'biblioid'    => @$lastBiblio['BiblioID'],
          'barcode'     => @$v['Barcode'],
          'location'    => @$v['Location'],
          'price'       => @$v['Price'],
          'dryrun'      => true,
        );
        $thisBiblioItem = $v;
        $thisBiblioItem['_res'] = lms_add_item_record($data);
      } else {
        $thisBiblioItem = $v;
        $thisBiblioItem['_res'] = ['success'=>false,'Biblio Record Error'];
      }

      $lastBiblio['_items'][] = $thisBiblioItem;
    }
    
  }
  echo json_encode(['success'=>true,'data'=>$res]);
  die();
} elseif (!empty($_POST['do']) && $_POST['do'] == 'import') {
  lms_check_csrf_token($_POST['csrf_token']);
  $arr = json_decode($_POST['data'],true);
  foreach ($arr as $v) {
    $id = (@$v['BiblioID']=='<<New>>' || !@$v['BiblioID']) ? NULL : $v['BiblioID'];
    $data = array(
      'biblioid'    => $id,
      'title'       => @$v['Title'],
      'callnumber'  => @$v['CallNo'],
      'author'      => @$v['Author'],
      'publisher'   => @$v['Publisher'],
      'isbn'        => @$v['ISBN'],
      'isrestricted'=> @$v['RestrictCheckOut'],
    );
    $q = lms_add_biblio_record($data);
    $biblioid = $q['data']['biblioid'];
    
    foreach ($v['_okItems'] as $item) {
      $id = (@$item['BiblioItemID']=='<<New>>' || !@$item['BiblioItemID']) ? NULL : $item['BiblioItemID'];
      $data = array(
        'biblioitemid'=> $id,
        'biblioid'    => @$biblioid,
        'barcode'     => @$item['Barcode'],
        'location'    => @$item['Location'],
        'price'       => @$item['Price'],
      );
      lms_add_item_record($data);
    }
  }
  echo json_encode(['success'=>true]);
  die();
}

$lms_layout_title = 'Bulk Upload Biblio/Item Records';

?>
<?php lms_layout_header('admin'); ?>

<div class="container">
  <div class="content-header">
    <!-- <h1 class="text-dark">
      &nbsp;
    </h1> -->
  </div>
  
  <div class="row" id="dataUploadDiv">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span>Select File</span>
          </h3>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label for="formControlFile"><?php _e('Select CSV file'); ?></label>
            <input type="file" class="form-control-file" id="formControlFile">
          </div>
          <hr>
          <button class="btn btn-sm btn-outline-primary" onclick="readCSVFile()"><?php _e('Process File'); ?></button>
          <a href="./../includes/biblio-sample.csv" download class="btn btn-sm btn-outline-secondary"><?php _e('Download Template'); ?></a>
        </div>
      </div>
    </div>
  </div>
  <!-- ========= -->
  <div class="row" id="dataStagingDiv" style="display:none;">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span>Check Records</span>
          </h3>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered" id="dataStagingTable">
            <thead>
              <tr>
                <th><?php _e('BiblioID'); ?></th>
                <th><?php _e('Title'); ?></th>
                <th><?php _e('Author'); ?></th>
                <th><?php _e('Publisher'); ?></th>
                <th><?php _e('Call Number'); ?></th>
                <th><?php _e('ISBN'); ?></th>
                <th><?php _e('Restrict Check-out'); ?></th>
                <th><?php _e('Status'); ?></th>
              </tr>
              <tr>
                <th colspan="3" class="text-right bg-light text-secondary"><?php _e('⮩'); ?></th>
                <th><?php _e('BiblioItemID'); ?></th>
                <th><?php _e('Barcode'); ?></th>
                <th><?php _e('Location'); ?></th>
                <th><?php _e('Price'); ?></th>
                <th><?php _e('Status'); ?></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          <hr>
          <p class="text-center" id="dataStagingTableRecordSum"></p>
          <div class="text-center">
            <button class="btn btn-primary" id="importValidRecordsBtn"  onclick="importValidRecords()"><?php _e('Import Valid Records'); ?></button>
            <button class="btn btn-outline-secondary" onclick="window.location.reload(true)"><?php _e('Reset'); ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ========= -->
</div>

<?php lms_layout_scripts('admin'); ?>
<script>
  'use strict';

  function CSVToArray( strData, strDelimiter ){
		strDelimiter = (strDelimiter || ",");
		var objPattern = new RegExp(
			(
				"(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +
				"(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +
				"([^\"\\" + strDelimiter + "\\r\\n]*))"
			),
			"gi"
			);
		var arrData = [[]];
		var arrMatches = null;
		while (arrMatches = objPattern.exec( strData )){
			// Get the delimiter that was found.
			var strMatchedDelimiter = arrMatches[ 1 ];
			if (
				strMatchedDelimiter.length &&
				(strMatchedDelimiter != strDelimiter)
				){
				arrData.push( [] );
			}
			if (arrMatches[ 2 ]){
				var strMatchedValue = arrMatches[ 2 ].replace(
					new RegExp( "\"\"", "g" ),
					"\""
					);
			} else {
				var strMatchedValue = arrMatches[ 3 ];
			}
			arrData[ arrData.length - 1 ].push( strMatchedValue );
		}
		return( arrData );
	}

  function readCSVFile(){
    var files = document.querySelector('#formControlFile').files;
    if(files.length > 0 ){
      var file = files[0];
      var reader = new FileReader();
      reader.readAsText(file);
      reader.onload = function(event) {
        var csvdata = event.target.result;
        processCSV(csvdata);
      };
    }else{
      Swal.fire('<?php _e('Please select a file.'); ?>');
    }
  }

  function processCSV(csvdata){
    let arr = CSVToArray(csvdata);
    if ((arr[0][0]??FALSE) !== 'BiblioID') {
      Swal.fire('<?php _e('File invalid.'); ?>','<?php _e(''); ?>','error');
      return false;
    }
    let headerArr = arr[0];
    let dataArr = [];
    arr.forEach((row,rowIndex) => {
      if (rowIndex==0) return;
      let dataArrNewRow = {};
      row.forEach((col,colIndex) => {
        dataArrNewRow[headerArr[colIndex]] = col;
      });
      dataArr.push(dataArrNewRow);
    });
    $.post(location.href,{do:'check',data:JSON.stringify(dataArr),csrf_token:"<?php echo lms_get_csrf_token(); ?>"})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          generateTable(arr.data);
        } catch (error) {
          console.error(error);
          Swal.fire('Error1',error.toString(),'error');
        }
      })
      .fail(function(data){
        Swal.fire('Error2');
      });
    
  }

  function generateTable(dataArr){
    $('#dataUploadDiv').hide();
    $('#dataStagingDiv').show();
    console.log(dataArr);
    $('#dataStagingTable>tbody').html('');
    let okRecords = 0;
    let ignoreRecords = 0;
    let totalRecords = 0;
    let okRecordsArr = [];
    dataArr.forEach(row => {
      $('#dataStagingTable>tbody').append(` 
        <tr>
          <td>${escapeHtml(row.BiblioID)}</td>
          <td>${escapeHtml(row.Title)}</td>
          <td>${escapeHtml(row.Author)}</td>
          <td>${escapeHtml(row.Publisher)}</td>
          <td>${escapeHtml(row.CallNo)}</td>
          <td>${escapeHtml(row.ISBN)}</td>
          <td>${escapeHtml(row.RestrictCheckOut)}</td>
          <td `+( (row._res.success??false)?'class="bg-success"':'class="bg-danger"' )+`>
          `+( (row._res.success??false)?'OK':'Error: '+escapeHtml(row._res.errormsg) )+`
          </td>
        </tr>
      `);
      if (row._res.success) {
        okRecords++;
        delete row['_res'];
        row['_okItems'] = [];
        okRecordsArr.push(row);
      } else {
        ignoreRecords++
      }
      totalRecords++;

      row._items.forEach(item => {
        $('#dataStagingTable>tbody').append(` 
          <tr>
            <td colspan="3" class="text-right bg-light text-secondary"><?php _e('⮩'); ?></td>
            <td>${escapeHtml(item.BiblioItemID)}</td>
            <td>${escapeHtml(item.Barcode)}</td>
            <td>${escapeHtml(item.Location)}</td>
            <td>${escapeHtml(item.Price)}</td>
            <td `+( (item._res.success??false)?'class="bg-success"':'class="bg-danger"' )+`>
            `+( (item._res.success??false)?'OK':'Error: '+escapeHtml(item._res.errormsg) )+`
            </td>
          </tr>
        `);
        if (item._res.success) {
          okRecords++;
          delete item['_res'];
          okRecordsArr[okRecordsArr.length - 1]['_okItems'].push(item);
        } else {
          ignoreRecords++
        }
        totalRecords++;
      });

      
      delete okRecordsArr[okRecordsArr.length - 1]['_items'];


    });
    $('#dataStagingTableRecordSum').text(sprintf('<?php echo addslashes(__('$ valid records, $ invalid records, $ total records processed')); ?>',okRecords, ignoreRecords, totalRecords));
    let arrKey = Math.random().toString();
    window.okRecordsArrKey = arrKey;
    window.okRecordsArr = okRecordsArr;
    $('#importValidRecordsBtn').data('key', arrKey);

  }

  function importValidRecords(){
    let okRecordsArrKey = window.okRecordsArrKey;
    let okRecordsArr = window.okRecordsArr;
    if (okRecordsArrKey != $('#importValidRecordsBtn').data('key')) {
      Swal.fire('error please refresh');
      return false;
    }
    $.post(location.href,{do:'import',data:JSON.stringify(okRecordsArr),csrf_token:"<?php echo lms_get_csrf_token(); ?>"})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success??false) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Imported Patron Records')); ?>',
            icon: 'success',
            willClose: function(){
              window.location.reload(true);
            }
          });
        } catch (error) {
          console.error(error);
          Swal.fire('Error1',error.toString(),'error');
        }
      })
      .fail(function(data){
        Swal.fire('Error2');
      });

  }


</script>
<?php lms_layout_footer('admin'); ?>

