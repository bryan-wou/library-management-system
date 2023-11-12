<script src="../plugins/jquery-3.2.1/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
<script src="../plugins/adminlte-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/adminlte-3.2.0/dist/js/adminlte.min.js"></script>
<script src="../plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="../plugins/sweetalert2/sweetalert2.all.min.js" crossorigin="anonymous"></script>
<script src="../plugins/daterangepicker/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script>
  $(function(){
    $('.nav-link-btn').each(function(){
      if ($(this).attr('href') == window.location.pathname.split('/').pop()) {
       $(this).addClass('active');
       $(this).closest('ul').closest('li').addClass('menu-open');
      }
    });
  });
  function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    
    try {
      return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    } catch (error) {
      return '';
    }
  }

  var sprintf = (str, ...argv) => !argv.length ? str : 
    sprintf(str = str.replace(sprintf.token||"$", argv.shift()), ...argv);

  function changeLang(locale){
    $.post('admin-ajax.php',{do:'changelocale',data:locale},function(){
      window.location.reload();
    });
  }

</script>