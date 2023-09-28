<!-- Footer -->
	<footer id="footer">
		<div class="copyright">
			<a href="http://phpjunction.com/phpcalendar/">PHP Calendar</a> Copyright &copy; <?php echo date("Y") ?> <a href="http://phpjunction.com">PHP junction</a>
		</div>
	</footer>
    <!-- Scripts -->
    <script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="/calendar/assets/js/jquery.fancybox.js"></script>
    <script type="text/javascript" src="/calendar/assets/js/skel.min.js"></script>
    <script type="text/javascript" src="/calendar/assets/js/main.js"></script>
    <script type="text/javascript" src="/calendar/assets/js/js_functions.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".iframe").fancybox();
            $(".picimg").fancybox({ maxHeight: 600 });
            $("#textmsg").fancybox({ padding: 10 });
            $("#textmsg").trigger('click');
            $("#viewregs").fancybox({
                'scrolling': 'no',
                'titleShow': false
            });
        });
    </script>
</body>
</html>
