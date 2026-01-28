</div><!-- End main-content -->

<script src="<?= $base_path ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base_path ?>assets/js/jquery.min.js"></script>
<script src="<?= $base_path ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $base_path ?>assets/plugins/datatables/dataTables.bootstrap5.min.js"></script>
<script src="<?= $base_path ?>assets/js/sweetalert2.min.js"></script>
<script src="<?= $base_path ?>assets/js/helpers.js"></script>
<script src="<?= $base_path ?>assets/js/main.js"></script>
<?php if (isset($data['page_js'])) { ?>
    <script src="<?= $base_path ?>assets/js/<?= $data['page_js'] ?>"></script>
<?php } ?>
</body>

</html>