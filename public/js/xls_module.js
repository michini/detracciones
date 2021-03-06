'use strict';

angular.module('xlsApp', ["angular-js-xlsx","ngFileSaver"], function($interpolateProvider) {
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	})
	.controller("exportCtrl", [
		"$scope",
		"$timeout",
		"$http",
		"FileSaver",
		"Blob",
		function(
			$scope,
			$timeout,
			$http,
			FileSaver,
			Blob,
		)
		{
			$scope.init = function() {
				$scope.uri = angular.element(document.querySelector("input[name='_url']")).val();
				$scope.types = [
					{
						type: 'format',
						name: 'Formato',
						skin: 'warning'
					},
					{
						type: 'manual',
						name: 'Manual',
						skin: 'success'
					}
				];
				$scope.types_sheets = [
					{
						type: 'provider',
						name: 'Proveedores'
					},
					{
						type: 'client',
						name: 'Clientes'
					}
				];
				$scope._token = angular.element(document.querySelector("input[name='_token']")).val();
				console.log($scope._token);
			};

			$scope.selectType = function(type) {
				angular.forEach($scope.types, function(item) {
					if (item.type == type) {
						$scope.type_selected = item;
					}
				});
				if ($scope.type_selected.type == 'manual') {
					$scope.provider = {
			        	id: undefined,
			        	name: undefined,
			        	ruc: undefined,
			        	account_number: undefined,
			        	lote: undefined,
			        	import: undefined,
			        	type: undefined,
			        	table: []
					};
				}
			};

			$scope.changeType = function() {
				$scope.type_selected = undefined;
				$scope.provider = undefined;
			};

			$scope.searchProvider = function() {
				$http.get($scope.uri+'/proveedores/'+$scope.provider.ruc).then(
		        	function(data) {
		        		if (data != undefined) {
			        		$scope.provider.id = data.data.id;
			        		$scope.provider.name = data.data.nombre;
			        		$scope.provider.account_number = data.data.nro_cuenta;
		        		}
		        	},
		        	function(error) {
		        		if(error.status = 404){
		        			toastr.error('RUC mal ingresado o no existe.','Error')
		        		}
		        		console.log(error.status);
		        	}
		        );
			};
			$scope.getColumnWidth = function(type) {
				return (type == 'provider') ? 5 : 6;
			};

			$scope.verifyExists = function() {
				var sum = 0;
				angular.forEach($scope.provider.table, function(item) {
					sum += (item.validated == undefined) ? 0 : 1 ;
				});
				return (sum == $scope.provider.table.length);
			};

			$scope.findProvider = function(ruc, row) {
				if (ruc != undefined) {
					if (ruc.length == 11) {
						// console.log(ruc);
						$http.get($scope.uri+'/proveedores/'+ruc).then(
		        	function(data) {
		        		if (data != undefined) {
			        		row.id = data.data.id;
			        		row.client.name = data.data.nombre;
			        		row.deposit.account = ($scope.provider.type == 'client') ? data.data.nro_cuenta : undefined;
		        		}
		        	},
		        	function(error) {
		        		if(error.status = 404){
		        			toastr.error('RUC mal ingresado o no existe.','Error')
		        		}

		        		console.log(error.status);
		        	}
		        );
						// console.log(ruc);
						console.log(ruc);
					}
				}
			};

			$scope.addRow = function() {
				console.log($scope.provider);
				$scope.provider.table.push(
					{
						client: {
						  type: undefined,
						  number: undefined,
						  name: undefined
						},
						deposit: {
						  proform: undefined,
						  service: undefined,
						  account: ($scope.provider.type == 'client')?0:undefined,
						  import: undefined,
						  operation: undefined,
						  period: undefined
						},
						voucher: {
						  comprobant: undefined,
						  serie: undefined,
						  comprobant_number: undefined
						}
					}
				);
			};

			$scope.getRowNumber = function() {
				// console.log($scope.provider.table.length);
				var _total_providers = _.filter($scope.provider.table, function(item) {
					return item.account == undefined
				});
				console.log(_total_providers);
				var _total_clients = _.filter($scope.provider.table, function(item) {
					return item.account != undefined
				});
				console.log(_total_clients);
				return ($scope.provider.type == 'client') ? _total_clients : _total_providers;
			};

			$scope.read = function (workbook) {
		     /* DO SOMETHING WITH workbook HERE */
        var _layout = workbook;
        $timeout(function() {
	        var _common_sheet = workbook.Sheets["Proveedor_Clientes"];
	        var _provider = {
	        	id: undefined,
	        	name: _common_sheet["B2"].v,
	        	ruc: _common_sheet["B3"].v,
	        	account_number: _common_sheet["B4"].v,
	        	lote: _common_sheet["K2"].v,
	        	import: undefined,
	        	sheets: [],
	        	type: undefined
	        };

	        var prov_id = undefined;
	        var _new_provider = {
    		method: 'POST',
		    	url: $scope.uri+'/proveedores',
		    	headers: {
		    		'X-CSRF-TOKEN': $scope._token
		    	},
		    	data: {
		    		ruc: _provider.ruc,
		    		nombre: _provider.name,
		    		cuenta: _provider.account_number
		    	}
	    	};

	    	$http(_new_provider).then(
	    		function(data){
	    			//console.log('leonel');
	    			//console.log(data.data.id_provider);
	    			prov_id = data.data.id_provider;
	    			console.log(prov_id);
	    		},
	    		function(error){
	    			console.log(error);
	    		}
	    	);
	    	console.log(prov_id);



        $http.get($scope.uri+'/proveedores/'+_provider.ruc).then(
        	function(data) {
        		_provider.id = data.data.id;
        		angular.forEach(workbook.Sheets, function(item, index) {
        			if (index != "Datos") {
        				var _sheet = {
        					name: index,
        					type: (index == 'Proveedor_Clientes') ? 'provider' : 'client',
        					table: [],
        					import: undefined
        				};
        				var last_row = (index == 'Proveedor_Clientes') ? item["!ref"].split(":L")[1] : item["!ref"].split(":M")[1];
		        		var initial_row = 11;
		        		for (var i = initial_row; i <= last_row; i++) {
		        			if (item["B"+i] != undefined) {
					        	var _row = {
					        		client: {
					        			type: item["B"+i].v,
						        		number: item["C"+i].v,
						        		name: item["D"+i].v
					        		},
					        		deposit: {
						        		proform: item["E"+i].v,
						        		service: item["F"+i].v,
						        		account: (index == 'Cliente_Proveedores') ? item["G"+i].v : undefined,
						        		import: (index == 'Proveedor_Clientes') ? item["G"+i].v : item["H"+i].v,
						        		operation: (index == 'Proveedor_Clientes') ? item["H"+i].v : item["I"+i].v,
						        		period: (index == 'Proveedor_Clientes') ? item["I"+i].v : item["J"+i].v
					        		},
					        		voucher: {
						        		comprobant: (index == 'Proveedor_Clientes') ? item["J"+i].v : item["K"+i].v,
						        		serie: (index == 'Proveedor_Clientes') ? item["K"+i].v : item["L"+i].v,
						        		comprobant_number: (index == 'Proveedor_Clientes') ? item["L"+i].v : item["M"+i].v
					        		}
					        	};
					        	_sheet.table.push(_row);
		        			}
				        }
				        _sheet.import = $scope.totalImport(_sheet);
				        _provider.sheets.push(_sheet);
        			}
        		});
        		$scope.provider = _provider;
        		$scope.selectSheet('provider');

        		//return false;
        	},
        	function(error) {
        		console.log(error);
        	}
        );

      });
	    };

	    $scope.selectSheet = function(sheet) {
	    	angular.forEach($scope.types_sheets, function(item) {
    			item.active = '';
	    	});

	    	angular.forEach($scope.types_sheets, function(item) {
	    		if (item.type == sheet) {
	    			item.active = 'active';
	    			$scope.sheet_selected = item;
	    			//console.log("ininid");
	    			$scope.choiceSheet(sheet);
	    		}
	    	});
	    };

	    $scope.choiceSheet = function(type) {
	    	$timeout(function() {
		    	angular.forEach($scope.provider.sheets, function(item) {
		    		if (item.type == type) {
		    			$scope.current_sheet = item;
		    			console.log($scope.current_sheet);
		    		}
		    	});
	    	});
	    };


	    $scope.totalImport = function(_array) {
	    	var sum = 0;
	    	angular.forEach(_array.table, function(item) {
	    		sum += item.deposit.import;
	    	});
	    	return sum;
	    };

	    $scope.getImport = function(type) {
				if ($scope.type_selected.type == 'format') {
					angular.forEach($scope.provider.sheets, function(item) {
						if (item.type == type) {
							return item.import;
						}
					});
				}
				if ($scope.type_selected.type == 'manual') {
					if ($scope.provider.table.length >0) {
						var sum_provider = 0;
						var sum_client = 0;
						angular.forEach($scope.provider.table, function(item) {
							if ($scope.provider.type == 'provider' && item.deposit.account == undefined)
							{
								console.log("provider");
								sum_provider += parseFloat(item.deposit.import);
							}
							else {
								console.log("client");
								sum_client += parseFloat(item.deposit.import);
							}
						});
						$scope.provider.import = ($scope.provider.type == 'client') ? sum_client : sum_provider;
					}
				}
	    };

	    $scope.error = function (e) {
	      /* DO SOMETHING WHEN ERROR IS THROWN */
	    	console.log(e);
	    };

	    $scope.returnFile = function() {
	    	$scope.provider = undefined;
	    };

			$scope.saveRow = function(row) {
				if (row.validated == undefined) {
					var _new = {
						method: 'POST',
						url: $scope.uri+'/detracciones',
						headers: {
							'X-CSRF-TOKEN': $scope._token
						},
						data: {
							tipo: row.client.type,
							numero: row.client.number,
							razon_social: row.client.name,
							nro_proforma: row.deposit.proform,
							bien_servicio: row.deposit.service,
							cuenta: (row.deposit.account == undefined) ? 'NULL' : row.deposit.account,
							importe: row.deposit.import,
							operacion: row.deposit.operation,
							periodo: row.deposit.period,
							comprobante: row.voucher.comprobant,
							serie: row.voucher.serie,
							comprobante_numero: row.voucher.comprobant_number,
							id_proveedor: $scope.provider.id
						}
					};

					$http(_new).then(
						function(data) {
							if(data.data.insert){
								toastr.success(data.data.msg+' '+data.data.id,'Correcto');
								$scope.getInsertId = data.data.id;
								console.log(data.data.id);
								row.id = data.data.id;
							}

							row.validated = data.data.insert;
							console.log(data);
						},
						function(error) {
							if(!data.data.insert){
								toastr.error('Intente nuevamente','Error');
							}
							console.log(error);
						}
					);
				}
				if (row.validated == false) {
					var _new = {
						method: 'PUT',
						url: $scope.uri+'/detracciones/'+row.id,
						headers: {
							'X-CSRF-TOKEN': $scope._token
						},
						data: {
							tipo: row.client.type,
							numero: row.client.number,
							razon_social: row.client.name,
							nro_proforma: row.deposit.proform,
							bien_servicio: row.deposit.service,
							cuenta: (row.deposit.account == undefined) ? 'NULL' : row.deposit.account,
							importe: row.deposit.import,
							operacion: row.deposit.operation,
							periodo: row.deposit.period,
							comprobante: row.voucher.comprobant,
							serie: row.voucher.serie,
							comprobante_numero: row.voucher.comprobant_number,
							id_proveedor: $scope.provider.id
						}
					};

					$http(_new).then(
						function(data) {
							if(data.data.updated){
								toastr.success(data.data.msg+' '+data.data.id,'Correcto');
								$scope.getInsertId = data.data.id;
								row.id = data.data.id;
								row.validated = true;
							}
						},
						function(error) {
							if(!data.data.updated){
								toastr.error('Intente nuevamente','Error');
							}
							console.log(error);
						}
					);
				}
			};

	    $scope.save = function() {

	    	angular.forEach($scope.provider.sheets, function(item) {
	    		angular.forEach(item.table, function(row) {
		    		$scope.saveRow(row);
	    		});
	    	});
	    };

	    $scope.verifyInsert = function() {
	    	var inserts = 0;
	    	angular.forEach($scope.provider.sheets, function(item) {
	    		angular.forEach(item.table, function(row) {
	    			inserts += (row.validated == undefined) ? 0 : 1;
	    		});
	    	});
	    	return (inserts > 0) ? true : false;
	    };

	    $scope.export = function(type) {
				if ($scope.type_selected.type == 'format') {
					var _export = undefined;
					angular.forEach($scope.provider.sheets, function(item) {
						if (item.type == type) {
							_export = item;
							_export.to_export = "";
							var _header = "";
							_header += "*"+$scope.provider.ruc+$scope.provider.name.substr(0,35)+$scope.provider.lote+$scope.provider.import;
							_export.to_export += _header+"\r\n";
							angular.forEach(_export.table, function(item) {
								var _proform = $scope.leading(item.deposit.proform,9);
								var _service = $scope.leading(item.deposit.service,3);
								var _account = (type == 'client') ? $scope.leading(item.deposit.account,3) : $scope.leading("0",3);
								var _import =  $scope.leading(item.deposit.import,15);
								var _operation =  $scope.leading(item.deposit.operation,2);
								var _comprobant =  $scope.leading(item.voucher.comprobant,2);
								var _number =  (item.voucher.comprobant_number.length > 8) ? item.voucher.comprobant_number.substr(item.voucher.comprobant_number.length-8,item.voucher.comprobant_number.length) : $scope.leading(item.voucher.comprobant_number,8);
								var text = "";
								var _row = "6"+item.client.number+text.substr(" ",35)+_proform+_service+_account+_import+_operation+item.deposit.period+_comprobant+item.voucher.serie+_number+"\r\n";
								_export.to_export += _row;
							});
							var data = new Blob([_export.to_export], { type: 'text/plain;charset=utf-8' });
							var filename = "D"+$scope.provider.ruc+$scope.provider.lote;
							FileSaver.saveAs(data,filename+".txt");
						}
					});
				}
				else {
					$scope.provider.to_export = "";
					var _header = "";
					var lote = ($scope.provider.lote.length <=6) ? $scope.leading($scope.provider.lote,6) : $scope.provider.lote.substr(0,6);
					var provider_name = ($scope.provider.name.length<=35) ? $scope.leadingSpacing($scope.provider.name, 35) : $scope.provider.name.substr(0.35);
					_header += "*"+$scope.provider.ruc+provider_name+lote+$scope.leading($scope.provider.import,15);
					$scope.provider.to_export += _header+"\r\n";
					angular.forEach($scope.provider.table, function(item) {
						// if (type == 'provider')
						var _proform = $scope.leading(item.deposit.proform,9);
						console.log(_proform);
						var _service = $scope.leading(item.deposit.service,3);
						console.log($scope.provider.type);
						var _account = ($scope.provider.type == 'client') ? $scope.leading(item.deposit.account,11) : $scope.leading("0",3);
						console.log(_account);
						var _import =  $scope.leading(item.deposit.import,15);
						var _operation =  $scope.leading(item.deposit.operation,2);
						var _comprobant =  $scope.leading(item.voucher.comprobant,2);
						var _number =  (item.voucher.comprobant_number.length > 8) ? item.voucher.comprobant_number.substr(item.voucher.comprobant_number.length-8,item.voucher.comprobant_number.length) : $scope.leading(item.voucher.comprobant_number,8);
						var text = '';
						var _row = "6"+item.client.number+$scope.leadingSpacing(" ",35)+_proform+_service+_account+_import+_operation+item.deposit.period+_comprobant+item.voucher.serie+_number+"\r\n";
						$scope.provider.to_export += _row;
					});
					var data = new Blob([$scope.provider.to_export], { type: 'text/plain;charset=utf-8' });
					var filename = "D"+$scope.provider.ruc+$scope.provider.lote;
					FileSaver.saveAs(data,filename+".txt");
				}
	    };

	    $scope.leading = function(num, size) {
		    var s = num+"";
		    while (s.length < size) s = "0" + s;
		    return s;
			};

			$scope.leadingSpacing = function(num, size) {
		    var s = num+"";
		    while (s.length < size) s = s + " ";
		    return s;
			}

			$scope.create_provider = function(){
				var new_prov = {
					method: 'POST',
			    	url: $scope.uri+'/provider_add',
			    	headers: {
			    		'X-CSRF-TOKEN': $scope._token
			    	},
			    	data: {
						nombre: $scope.new_provider_razon,
						cuenta: $scope.new_provider_account,
						ruc: $scope.new_provider_ruc,
						data_sent: true
			    	}
				};

				$http(new_prov).then(
					function(data){
						toastr.success(data.data.msg,'Mensaje');
						$('#myModal').modal('hide');
						console.log(data);
					},
					function(error){

						toastr.error(!jQuery.isEmptyObject(error.data.ruc) ? error.data.ruc : ' ','Error')
						toastr.error(!jQuery.isEmptyObject(error.data.nombre) ? error.data.nombre : ' ','Error')
						toastr.error(!jQuery.isEmptyObject(error.data.cuenta) ? error.data.cuenta : ' ','Error')

						console.log(error.data.ruc.length);
						console.log(error);
					}
				)
			}

		}
	])
