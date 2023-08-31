<!-- Footer -->
	<footer id="footer">
		<div class="copyright">
			<a href="http://phpjunction.com/webapps/">PHP Calendar</a> Copyright &copy; <?php echo date("Y") ?> <a href="http://phpjunction.com">PHP junction</a>
		</div>
	</footer>
  <!-- Scripts -->
  <script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.js"></script>
  <script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript" src="../assets/js/jquery.fancybox.js"></script>
	<script type="text/javascript" src="../assets/js/skel.min.js"></script>
	<script type="text/javascript" src="../assets/js/main.js"></script>
  <script type="text/javascript" src="../assets/js/js_functions.js" ></script>
  <script type="text/javascript">
  $(document).ready(function(){
	  $(".iframe").fancybox();
	  $(".picimg").fancybox();
		$("#textmsg").fancybox({padding: 0, border: 1, margin: 10 });
	  $("#textmsg").trigger('click');
		$( document ).tooltip();
  });
  </script>
</body>
</html>
