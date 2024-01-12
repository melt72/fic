		<!-- Basic modal -->
		<div class="modal effect-scale" id="modal-logout">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h6 class="modal-title">Conferma Uscita</h6><button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<h6>Logout</h6>
						<p>Vuoi uscire?</p>
					</div>
					<div class="modal-footer">
						<a href="logout.php" class="btn ripple btn-primary" type="button">Si</a>
						<button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Chiudi</button>
					</div>
				</div>
			</div>
		</div>
		<!-- End Basic modal -->

		<!-- Basic modal -->
		<div class="modal effect-scale" id="modal-zone">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h6>Creazione/Mod Zona</h6>
						<button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div class="container">
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label for="nome_zona" class="form-label">Zona</label>
										<input type="text" class="form-control" name="nome_zona" id="nome_zona" placeholder="Nome della zona">
									</div>
									<div class="mb-3">
										<label for="" class="form-label">Ripartizione predefinita</label>
										<select class="form-select form-select" name="provv" id="provv">
											<option value="1">50 % agente 50 % agenzia</option>
											<option value="2">Tutto agenzia</option>
											<option value="3">Caso speciale(50%/zone-50% agenzia)</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="modal-footer" class="modal-footer">
						<button class="btn ripple btn-primary salvazona" type="button" id="btn-save-zone">Salva</button>
						<button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Chiudi</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Basic modal -->
		<div class="modal effect-scale" id="modal-cliente">
			<div class="modal-dialog  modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h6>Associazione Cliente</h6>
						<button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div class="table-responsive">
							<table class="table border-top-0 table-bordered text-nowrap border-bottom" id="clienti-datatable">
								<thead>
									<tr>
										<th class="wd-15p border-bottom-0">Nome cliente</th>
										<th class="wd-15p border-bottom-0">Città</th>
										<th class="wd-20p border-bottom-0">Prov</th>
										<th class="wd-15p border-bottom-0">Status</th>
										<th class="wd-10p border-bottom-0"></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$clienti = get_clienti_totali('RM');
									if ($clienti) :
										foreach ($clienti as $cliente) :
									?>
											<tr">
												<td><?= $cliente['nome'] ?></td>
												<td><?= $cliente['citta'] ?></td>
												<td><?= $cliente['provincia'] ?></td>
												<td id="cl<?= $cliente['id'] ?>"><?= $cliente['associato'] ?></td>
												<?php
												if ($cliente['associato'] != '--') :
												?>
													<td><button class=" btn btn-danger btn-sm disassociacliente" data-cliente="<?= $cliente['id'] ?>">Disassocia</button></td>
												<?php
												else : ?>
													<td><button class=" btn btn-primary btn-sm associacliente" data-cliente="<?= $cliente['id'] ?>">Associa</button></td>
												<?php
												endif;
												?>
												</tr>
										<?php
										endforeach;
									endif;
										?>

								</tbody>
							</table>
						</div>
					</div>


					<div class="modal-footer">

					</div>
				</div>
			</div>
		</div>

		<!-- Large Modal -->
		<div class="modal" id="modal-liquidazione">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">Large Modal</h6><button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div class="row mg-b-20">
							<div class="col-md-6">
								<label for="data">Data</label>
								<div class="input-group">
									<div class="input-group-text">
										<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
									</div>
									<input class="form-control" id="data_liquidazione" name="data_liquidazione" placeholder="MM/DD/YYYY" type="text" required>
								</div>
							</div>
							<div class="col-md-6 mg-t-25">
								<h2>IMPORTO: <span id="importo_liquidazione"><?= isset($id_agente) ? arrotondaEFormatta(getTotaleLiquidazioneAgente($id_agente)) : '' ?></span> €</h2>
							</div>
						</div>
						<div class="row  mg-b-20">
							<div class="col-md-6">
								<label for="metodo_pagamento">Metodo di pagamento</label>
								<select class="form-select select2-no-search" name="metodo_pagamento" id="metodo_pagamento">
									<option value="">Scegli metodo</option>
									<option value="1">Bonifico</option>
									<option value="2">Assegno</option>
									<option value="3">Contanti</option>
								</select>
							</div>
							<div class="col-md-6"><label for="note">Note</label>
								<input class="form-control" placeholder="Textarea" id="note" name="note"></input>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-bordered border text-nowrap mb-0" id="basic-edittable">
										<thead>
											<tr>
												<th>Cliente</th>
												<th>Fattura</th>
												<th>Importo</th>
												<th>Prov %</th>
												<th>Prov €</th>
												<th></th>
											</tr>
										</thead>
										<tbody id="dati_fatture">
											<?php
											if (isset($id_agente)) :
												$fatture_da_liquidare = getFattureDaLiquidareAgente($id_agente);
												foreach ($fatture_da_liquidare as $fattura) {
													$provvigione = arrotondaEFormatta($fattura['provvigione']);
											?>
													<tr>
														<td>Bella</td>
														<td>N° <?= $fattura['num_f'] ?> del<br> <?= date('d/m/Y', strtotime($fattura['data_f'])) ?></td>
														<td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
														<td><?= $fattura['provv_percent'] ?> %</td>
														<td><?= $provvigione ?> €</td>
														<td><i class="fe fe-check-square text-success li-scelta inclusa" data-id="<?= $fattura['id_fatt'] ?>" data-importo="<?= $provvigione ?>" data-bs-toggle="tooltip" title="" data-bs-original-title="fe fe-check-square" aria-label="fe fe-check-square"></i></td>
													</tr>
											<?php
												}
											endif;
											?>
										</tbody>
									</table>
								</div> <!-- Tabella con le fatture da liquidare -->
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-primary liquida_provv" type="button">Liquida Provvigione</button>
						<button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Chiudi</button>
					</div>
				</div>
			</div>
		</div>
		<!--End Large Modal -->

		<div class="modal" id="modal-liquidazione-zona">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">Large Modal</h6><button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div class="row mg-b-20">
							<div class="col-md-6">
								<label for="data">Data</label>
								<div class="input-group">
									<div class="input-group-text">
										<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
									</div>
									<input class="form-control" id="data_liquidazione_zona" name="data_liquidazione_zona" placeholder="MM/DD/YYYY" type="text" required>
								</div>
							</div>
							<div class="col-md-6">
								<!-- <?php
										$provvigione_totale2 = getTotaleLiquidazioneZona($id_zona);
										?>
								<h3>IMPORTO TOT: <span id="importo_liquidazione"><?= isset($id_zona) ? arrotondaEFormatta($provvigione_totale2['totale']) : '' ?></span> €</h3>
								<h5>IMPORTO AGENTE: <span id="importo_liquidazione_agente"><?= isset($id_zona) ? arrotondaEFormatta($provvigione_totale2['a']) : '' ?></span> €</h5>
								<h5>IMPORTO AGENZIA: <span id="importo_liquidazione_ag"><?= isset($id_zona) ? arrotondaEFormatta($provvigione_totale2['b']) : '' ?></span> €</h5> -->
							</div>
						</div>
						<div class="row  mg-b-20">
							<div class="col-md-6">
								<label for="metodo_pagamento">Metodo di pagamento</label>
								<select class="form-select select2-no-search" name="metodo_pagamento" id="metodo_pagamento">
									<option value="">Scegli metodo</option>
									<option value="1">Bonifico</option>
									<option value="2">Assegno</option>
									<option value="3">Contanti</option>
								</select>
							</div>
							<div class="col-md-6"><label for="note">Note</label>
								<input class="form-control" placeholder="Textarea" id="note" name="note"></input>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-bordered border text-nowrap mb-0" id="basic-edittable">
										<thead>
											<tr>
												<th>Zona</th>
												<th>Prov Agente</th>
												<th>Prov Agenzia</th>
												<th></th>
											</tr>
										</thead>
										<tbody id="dati_fatture">
											<?php
											$zone = getTotaleLiquidazioneZoneRoma(); // Prendo tutte le zone
											foreach ($zone as $zona) {
											?>
												<tr>
													<td><?= $zona['nome'] ?></td>
													<td><?= arrotondaEFormatta($zona['a']) ?> €</td>
													<td><?= arrotondaEFormatta($zona['b']) ?> €</td>
													<td></td>
												</tr>
											<?php
											}
											?>
										</tbody>
									</table>
								</div> <!-- Tabella con le fatture da liquidare -->
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-primary liquida_provv" type="button">Liquida Provvigione</button>
						<button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Chiudi</button>
					</div>
				</div>
			</div>
		</div>