@extends('layouts.app')
@section('content')
{{csrf_field()}}
	<div class="container-fluid" ng-app="xlsApp" ng-controller="exportCtrl" ng-init="init()">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Panel control
					<span class="pull-right text-danger" ng-if="type_selected != undefined">&nbsp;| <span style="cursor: pointer;" ng-click="changeType()">Cambiar Eleccion</span></span>
					<strong class="pull-right" ng-if="type_selected != undefined">Tipo: <% type_selected.name %></strong>
					<input type="hidden" name="_url" value="{{url('')}}">
				</div>
				<div class="panel-body">
					<div class="row" ng-if="type_selected == undefined">
						<div class="col-sm-offset-3 col-sm-6" ng-repeat="type in types">
							<div class="panel panel-<% type.skin %>" style="cursor: pointer;" ng-click="selectType(type.type)">
								<div class="panel-body bg-<% type.skin %> text-center">
									<% type.name %>
								</div>
							</div>
						</div>
					</div>
					<div class="row" ng-if="type_selected != undefined">
						<div class="col-sm-12" ng-if="provider == undefined && type_selected.type == 'format'">
						  	<span class="col-sm-6">Seleccione el documento en excel para validar la informacion</span> &nbsp;
						  	<js-xls class="btn btn-default col-sm-4" onread="read" onerror="error"></js-xls>
						</div>
						<div class="col-sm-12" ng-if="provider != undefined">
							<form class="form-horizontal">
							<div class="btn-group btn-group-xs pull-right" role="group" aria-label="...">
								<button class="btn btn-default" type="button" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="glyphicon glyphicon-plus-sign"></i> Nuevo proveedor</button>
								<button class="btn btn-danger" ng-click="returnFile()" ng-if="type_selected.type == 'format'"><i class="glyphicon glyphicon-book"></i> Cambiar documento</button>
							</div>
							<!--<span class="pull-right text-danger" style="cursor: pointer;" ng-click="returnFile()" ng-if="type_selected.type == 'format'">Cambiar documento</span>-->
							<h3>Datos del Proveedor <% provider.id %></h3>
							  <div class="form-group">
							    <label for="ruc" class="col-sm-2 control-label">RUC:</label>
							    <div class="col-sm-2">
							      <input type="text" class="form-control" ng-if="type_selected.type == 'format'" id="ruc" ng-model="provider.ruc" readonly="">
							      <div class="input-group" ng-if="type_selected.type == 'manual'">
							      	<input type="text"  maxlength="11" pattern="[0-9]{11}" class="form-control" ng-if="type_selected.type == 'manual'" id="ruc" ng-model="provider.ruc">
							      	<span class="input-group-btn">
							      		<span class="btn btn-default" ng-click="searchProvider()">
							      			<span>Buscar</span>
							      		</span>
							      	</span>
							      </div>
							    </div>

							    <label for="lote" class="col-sm-2 col-sm-offset-1 control-label">Nº lote:</label>
							    <div class="col-sm-2">
							      <input type="text" class="form-control" ng-if="type_selected.type =='format'" id="lote" ng-model="provider.lote" readonly="">
							      <input type="text" class="form-control" ng-if="type_selected.type =='manual' && provider.name == undefined" id="lote" ng-model="provider.lote" readonly="">
							      <input type="text" class="form-control" ng-if="type_selected.type =='manual' && provider.name != undefined" id="lote" ng-model="provider.lote">
							    </div>

							  </div>
							  <div class="form-group">
							    <label for="proveedor" class="col-sm-2 control-label">Proveedor:</label>
							    <div class="col-sm-3">
							      <input type="text" class="form-control" id="proveedor" ng-model="provider.name" readonly="readonly">
							    </div>

							    <label for="importe" class="col-sm-2 control-label">Importe:</label>
							    <div class="col-sm-2">
							      <input type="text" class="form-control" id="importe" ng-if="type_selected.type =='format'" value="<% current_sheet.import | number:'2'%>" readonly="">
							      <input type="text" class="form-control" id="importe" ng-if="type_selected.type =='manual'" ng-model="provider.import"readonly="">
							    </div>
							  </div>
							  <div class="form-group">
							    <label for="cuenta" class="col-sm-2 control-label">Nº cuenta:</label>
							    <div class="col-sm-2">
							      <input type="text" class="form-control " id="cuenta" ng-model="provider.account_number" readonly="">
							    </div>
							    <div class="col-sm-3 radio text-center" ng-if="type_selected.type == 'manual'">
								    <label>
								    	<input type="radio" name="type" value="provider" ng-model="provider.type">
								    	Proveedor
								    </label>
							    </div>
							    <div class="col-sm-3 radio text-center" ng-if="type_selected.type == 'manual'">
								    <label>
								    	<input type="radio" name="type" value="client" ng-model="provider.type">
								    	Cliente
								    </label>
							    </div>
							  </div>

							</form>
						</div>
						<div ng-if="provider != undefined && type_selected.type == 'manual'">
							<div class="col-sm-12" ng-if="provider.name != undefined && provider.lote != undefined && provider.type != undefined">
								<div class="form-group">
									<button class="btn btn-default" ng-click="addRow()">Agregar Fila</button>
									<button class="pull-right btn btn-primary btn-sm" ng-if="verifyExists() == true && provider.table.length>0" ng-click="export(current_sheet.type)">
										Export en .txt
									</button>
								</div>
								<hr>
								<div class="table-responsive" ng-if="provider.table.length >0 " style="max-height:400px; overflow:auto;">
								  <table class="table table-bordered">
								  	<thead>
								  		<tr style="background-color: #444; color:#fff;">
								  			<th  class="text-center" colspan="3">Datos del Cliente</th>
								  			<th  class="text-center" colspan="<% getColumnWidth(provider.type) %>">Informacion acerca del Deposito</th>
								  			<th  class="text-center" colspan="3">Datos del Comprobante</th>
								  			<th class="text-center" rowspan="2" style="vertical-align: middle">Acción</th>
								  		</tr>
								  		<tr style="background-color: #444; color:#fff;">
								  			<th  class="text-center">Tipo</th>
								  			<th  class="text-center">Numero</th>
								  			<th  class="text-center">Nombre / Razon Social</th>
								  			<th  class="text-center">Prof.</th>
								  			<th  class="text-center">B/S</th>
								  			<th  class="text-center" ng-if="provider.type == 'client'">Cuenta</th>
								  			<th  class="text-center">Imp</th>
								  			<th  class="text-center">Tipo</th>
								  			<th  class="text-center">Period</th>
								  			<th  class="text-center">Tipo</th>
								  			<th  class="text-center">Serie</th>
								  			<th  class="text-center">Numero</th>
								  		</tr>
								  	</thead>
								  	<tbody class="tabla">
								  		<form name="new_row">
									  		<tr ng-repeat="row in provider.table" ng-if="(row.deposit.account == 0 && provider.type == 'client') || (row.deposit.account == undefined && provider.type == 'provider')">
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.client.type" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.client.type %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 110px;" class="form-control" name="" ng-model="row.client.number" ng-keyup="findProvider(row.client.number, row)" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.client.number %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 250px;" class="form-control" name="" ng-model="row.client.name" readonly="" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.client.name %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.deposit.proform" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.deposit.proform %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.deposit.service" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.deposit.service %></span>
									  			</td>
													<td ng-if="provider.type == 'client'">
														<input type="text" style="width: 60px;" class="form-control" name="" ng-model="row.deposit.account" readonly="" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.deposit.account %></span>
													</td>
									  			<td>
									  				<input type="text" style="width: 60px;" class="form-control" name="" ng-model="row.deposit.import" ng-keyup="getImport()" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.deposit.import %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.deposit.operation" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.deposit.operation %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 80px;" class="form-control" name="" ng-model="row.deposit.period" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.deposit.period %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.voucher.comprobant" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.voucher.comprobant %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 80px;" class="form-control" name="" ng-model="row.voucher.serie" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.voucher.serie %></span>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 60px;" class="form-control" name="" ng-model="row.voucher.comprobant_number" required ng-if="row.validated == false || row.validated == undefined">
														<span ng-if="row.validated == true"><% row.voucher.comprobant_number %></span>
									  			</td>
													<td class="text-center">
														<span class="btn btn-sm btn-default" ng-click="saveRow(row)">
															<span class="glyphicon glyphicon-floppy-save"></span>
														</span>
														<span class="btn btn-sm btn-default" ng-click="row.validated = false">
															<span class="glyphicon glyphicon-pencil"></span>
														</span>
													</td>
									  		</tr>
									  	</form>
								  	</tbody>
								  </table>
								</div>
							</div>
						</div>
						<div class="col-sm-12" ng-if="provider != undefined && type_selected.type == 'format'">
							<hr>
							&nbsp;<button class="pull-right btn btn-success btn-sm" value="Guardar" ng-click="save()" ng-disabled="verifyInsert() == true">
								Guardar
							</button>&nbsp;
							<h3>
								Estado del Archivo Excel
							</h3>

							<button class="pull-right btn btn-primary btn-sm" ng-if="verifyInsert() == true" ng-click="export(current_sheet.type)">
								Export en .txt
							</button>
							<ul class="nav nav-tabs">
								<li class="<% type.active %>" ng-repeat="type in types_sheets" ng-click="selectSheet(type.type)">
									<a href=""><% type.name%></a>
								</li>
							</ul>
							<div class="table-responsive" style="max-height:400px; overflow:auto;" ng-if="current_sheet != undefined">
							  <table class="table table-striped">
							  	<thead>
							  		<tr>
							  			<th>Nº</th>
							  			<th>Tipo</th>
							  			<th>Numero</th>
							  			<th>Nombre / Razon Social</th>
							  			<th>Prof.</th>
							  			<th>B/S</th>
							  			<th ng-if="current_sheet.type == 'client'">Cuenta</th>
							  			<th>Importe</th>
							  			<th>Tipo</th>
							  			<th>Period</th>
							  			<th>Tipo</th>
							  			<th>Serie</th>
							  			<th>Numero</th>
							  		</tr>
							  	</thead>
							  	<tbody style="max-height:300px; overflow:auto;">
							  		<tr ng-repeat="row in current_sheet.table" ng-class="{'bg-danger': row.validated == false, 'bg-success': row.validated == true}">
							  			<td><% $index+1 %></td>
										<td><% row.client.type %></td>
										<td><% row.client.number %></td>
										<td><% row.client.name %></td>
										<td><% row.deposit.proform %></td>
										<td><% row.deposit.service %></td>
										<td ng-if="current_sheet.type == 'client'"><% row.deposit.account %></td>
										<td><% row.deposit.import %></td>
										<td><% row.deposit.operation %></td>
										<td><% row.deposit.period %></td>
										<td><% row.voucher.comprobant %></td>
										<td><% row.voucher.serie %></td>
										<td><% row.voucher.comprobant_number %></td>
							  		</tr>
							  	</tbody>
							  </table>
							</div>
						</div>
					</div>
					  <!--<div class="form-group">
					    <div class="col-sm-offset-2 col-sm-10">
					      <button type="submit" class="btn btn-default">Sign in</button>
					    </div>
					  </div>-->

				</div>
			</div>
		</div>
					<!-- start modal -->
					<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
					  <div class="modal-dialog modal-sm" role="document" style="left: 0%;">
					    <div class="modal-content">
					     		<div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        <h4 class="modal-title">Nuevo proveedor</h4>
						      </div>
						      <div class="modal-body">
						        <form class="form-horizontal">
						        	<div class="container">
						        		<div class="col-sm-12">
						        			<div class="form-group">
						        				<label class="control-label col-sm-2">RUC:</label>
						        				<div class="col-sm-3">
						        					<input type="text" class="form-control" ng-model="new_provider_ruc" autocomplete="off">
						        				</div>
						        			</div>
						        			<div class="form-group">
						        				<label class="control-label col-sm-2">Razon social:</label>
						        				<div class="col-sm-3">
						        					<input type="text" class="form-control" ng-model="new_provider_razon" autocomplete="off">
						        				</div>
						        			</div>
						        			<div class="form-group">
						        				<label class="control-label col-sm-2">Cuenta:</label>
						        				<div class="col-sm-3">
						        					<input type="text" class="form-control" ng-model="new_provider_account" autocomplete="off">
						        				</div>
						        			</div>
						        			<div class="form-group">
						        				<div class="col-sm-3 col-sm-offset-2">
						        					<button type="button" class="btn btn-primary btn-sm" ng-click="create_provider()"><i class="glyphicon glyphicon-floppy-disk"></i> Guardar</button>
						        				</div>
						        			</div>
						        		</div>
						        	</div>
						        </form>
						      </div>
					    </div>
					  </div>
					</div>
					<!-- end modal -->
	</div>
@endsection
@section('js')
<script type="text/javascript">
	$('document').ready(function(){
		$('#myModal').modal('show');
	});


</script>
@endsection
