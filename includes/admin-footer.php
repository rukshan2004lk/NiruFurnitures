<?php
/**
 * NiRu-Furnitures — Admin Footer
 */
$extraAdminJs = $extraAdminJs ?? [];
?>

</main><!-- /.admin-main -->
</div><!-- /.admin-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmMpZJKpABiyY8PGpaDV+VhE0pN/"
    crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<script src="<?= SITE_URL ?>/assets/js/admin.js"></script>

<?php foreach ($extraAdminJs as $js): ?>
    <script src="<?= e($js) ?>"></script>
<?php endforeach; ?>

</body>

</html>