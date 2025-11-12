<!-- application/views/admin/_partials/footer.php -->
  </main> <!-- /main -->
</div> <!-- /content-wrap -->
</div> <!-- /layout -->

<!-- jQuery -->
<script src="<?= base_url('assets/vendor/jquery-3.7.1.min.js') ?>"></script>
<script>if(!window.jQuery){document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>');}</script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables (client-side only) -->
<link  href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link  href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- Page JS -->
<?php if (!empty($page_js)): foreach ($page_js as $js): ?>
  <script src="<?= base_url($js) ?>"></script>
<?php endforeach; endif; ?>

</body>
</html>
