<?php
include('partial/headerarea.php'); ?>
<?php include('partial/header.php'); ?>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include('partial/navbar.php'); ?>
    <?php include('partial/menu.php'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header sticky-top mb-2" style="background-color: white" ;>
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Anagrafica</h1>
            </div><!-- /.col -->
            <div class="col-sm-6 text-right">
              <a href="add_anagrafica.php" class="btn btn-warning btn-sm"><i class="fas fa-plus-circle"></i> Nuovo</a>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <!-- <div class="card-header">
                  <h3 class="card-title">DataTable with default features</h3>
                </div> -->
                <!-- /.card-header -->
                <?php
                $clienti = clienti();
                ?>
                <div class="card-body">
                  <table id="example1" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>RS / Nome</th>
                        <th>P.IVA</th>
                        <th>Tel</th>
                        <th>E-mail</th>
                        <th>Indirizzo</th>
                        <th>Città</th>
                        <th>PV</th>
                        <!-- <th class="dt-filter">Platform(s)</th>
                        <th class="dt-filter">Engine version</th> -->
                        <th>Azioni</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($clienti as $row) { ?>
                        <tr>
                          <td></td>
                          <td><?= $row['rs'] == '' ? $row['nome'] . ' ' . $row['cognome'] : $row['rs'] ?></td>
                          <td><?= $row['piva'] == '' ? '' : 'Piva: ' . $row['piva'] . '<br>' ?><?= $row['cf'] == '' ? '' : 'CF: ' . $row['cf'] ?>
                          </td>
                          <td><?= $row['tel'] ?></td>
                          <td><?= $row['email'] == '' ? '' : 'E-mail: ' . $row['email'] . '<br>' ?><?= $row['pec'] == '' ? '' : 'PEC: ' . $row['pec'] ?></td>
                          <td><?= $row['indirizzo'] ?></td>
                          <td><?= $row['citta'] ?></td>
                          <td><?= $row['pv'] ?></td>
                          <td width="95px"><a href="add_anagrafica.php?id=<?= $row['idcliente'] ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-square  black"></i></a><a href="add_anagrafica.php?id=<?= $row['idcliente'] ?>" class="btn btn-light btn-sm red"><i class="fas fa-trash black"></i></a> </td>
                        </tr>
                      <?php } ?>
                      </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
            </div>

          </div>
          <!-- /.row -->


        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include('partial/footer.php'); ?>
    <?php include('partial/modal.php'); ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->
  <?php
  include 'partial/librerie.php'
  ?>
</body>

</html>
<script>
  $(function() {
    $('#example1').DataTable({
      language: {
        url: 'http://cdn.datatables.net/plug-ins/1.12.1/i18n/it-IT.json'
      },
      initComplete: function() {
        this.api()
          .columns('.dt-filter')
          .every(function() {
            var column = this;
            var select = $('<select><option value="">Tipo</option></select>')
              .appendTo($(column.header()).empty())
              .on('change', function() {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function(d, j) {
                select.append('<option value="' + d + '">' + d + '</option>');
              });
          });
      },
      "responsive": true,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>