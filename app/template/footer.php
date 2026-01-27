</div><!-- End main-content -->

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap5.min.js"></script>
<script src="assets/js/sweetalert2.min.js"></script>
<script src="assets/js/main.js"></script>
<?php if (isset($data['page_js'])) { ?>
    <script src="assets/js/<?= $data['page_js'] ?>"></script>
<?php } ?>
</body>

</html>