@extends('layouts.app')
@section('content')
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
							<span class="pull-right text-danger" style="cursor: pointer;" ng-click="returnFile()" ng-if="type_selected.type == 'format'">Cambiar documento</span>
							<h2>Datos del Proveedor <% provider.id %></h2>
							  <div class="form-group">
							    <label for="ruc" class="col-sm-2 control-label">RUC:</label>
							    <div class="col-sm-4">
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

							    <label for="lote" class="col-sm-2 control-label">Nº lote:</label>
							    <div class="col-sm-4">
							      <input type="text" class="form-control" ng-if="type_selected.type =='format'" id="lote" ng-model="provider.lote" readonly="">
							      <input type="text" class="form-control" ng-if="type_selected.type =='manual' && provider.name == undefined" id="lote" ng-model="provider.lote" readonly="">
							      <input type="text" class="form-control" ng-if="type_selected.type =='manual' && provider.name != undefined" id="lote" ng-model="provider.lote">
							    </div>
							  </div>
							  <div class="form-group">
							    <label for="proveedor" class="col-sm-2 control-label">Proveedor:</label>
							    <div class="col-sm-4">
							      <input type="text" class="form-control" id="proveedor" ng-model="provider.name" readonly="readonly">
							    </div>

							    <label for="importe" class="col-sm-2 control-label">Importe:</label>
							    <div class="col-sm-4">
							      <input type="text" class="form-control" id="importe" value="<% current_sheet.import | number:'2'%>" readonly="">
							    </div>
							  </div>
							  <div class="form-group">
							    <label for="cuenta" class="col-sm-2 control-label">Nº cuenta:</label>
							    <div class="col-sm-4">
							      <input type="text" class="form-control " id="cuenta" ng-model="provider.account_number" readonly="">
							    </div>
							    <div class="col-sm-3 radio text-center" ng-if="type_selected.type == 'manual'">
								    <label>
								    	<input type="radio" name="type" value="provider" ng-model="provide.type">
								    	Proveedor
								    </label>
							    </div>
							    <div class="col-sm-3 radio text-center" ng-if="type_selected.type == 'manual'">
								    <label>
								    	<input type="radio" name="type" value="client" ng-model="provide.type">
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
								</div>
								<hr>
								<div class="table-responsive" ng-if="provider.table.length >0 " style="max-height:400px; overflow:auto;">
								  <table class="table table-bordered">
								  	<thead>
								  		<tr style="background-color: #444; color:#fff;">
								  			<th  class="text-center" rowspan="2">Nº</th>
								  			<th  class="text-center" colspan="3">Datos del Cliente</th>
								  			<th  class="text-center" colspan="5">Informacion acerca del Deposito</th>
								  			<th  class="text-center" colspan="3">Datos del Comprobante</th>
								  		</tr>
								  		<tr style="background-color: #444; color:#fff;">
								  			<th  class="text-center">Tipo</th>
								  			<th  class="text-center">Numero</th>
								  			<th  class="text-center">Nombre / Razon Social</th>
								  			<th  class="text-center">Prof.</th>
								  			<th  class="text-center">B/S</th>
								  			<th  class="text-center">Imp</th>
								  			<th  class="text-center">Tipo</th>
								  			<th  class="text-center">Period</th>
								  			<th  class="text-center">Tipo</th>
								  			<th  class="text-center">Serie</th>
								  			<th  class="text-center">Numero</th>
								  			
								  		</tr>
								  	</thead>
								  	<tbody>
								  		<form name="new_row">
									  		<tr ng-repeat="row in provider.table">
									  			<td>
									  				<% $index + 1 %>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.tipo_operacion_proveedor" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 110px;" class="form-control" name="" ng-model="row.ruc_proveedor" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 250px;" class="form-control" name="" ng-model="row.razon_social" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.numero_proforma" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.bien_servicio" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 60px;" class="form-control" name="" ng-model="row.importe" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.tipo_operacion" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 80px;" class="form-control" name="" ng-model="row.periodo" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 40px;" class="form-control" name="" ng-model="row.tipo_comprobante" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 80px;" class="form-control" name="" ng-model="row.serie" required>
									  			</td>
									  			<td>
									  				<input type="text" style="width: 60px;" class="form-control" name="" ng-model="row.numero_comprobante" required>
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
							<h2>
								Estado del Archivo Excel 
							</h2>
							{{csrf_field()}}
							<button class="pull-right btn btn-default btn-sm" ng-if="verifyInsert() == true" ng-click="export(current_sheet.type)">
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
	</div>
@endsection
@section('js')

@endsection