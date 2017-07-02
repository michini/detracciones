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
		        		console.log(error);
		        	}
		        );
			};

			$scope.addRow = function() {
				$scope.provider.table.push(
					{
						tipo_operacion_proveedor: undefined,
						ruc_proveedor: undefined,
						razon_social: undefined,
						numero_proforma: undefined,
						bien_servicio: undefined,
						cuenta_proveedor: $scope.provider.account_number,
						importe_proveedor: undefined,
						tipo_operacion: undefined,
						periodo: undefined,
						tipo_comprobante: undefined,
						numero_comprobante: undefined,
						lote: $scope.provider.lote,
						serie: undefined,
						importe: undefined,
						id_proveedor: $scope.provider.id,
					}
				);
			};

			$scope.read = function (workbook) {
		     /* DO SOMETHING WITH workbook HERE */
		        var _layout = workbook;
		        var _token = angular.element(document.querySelector("input[name='_token']")).val();
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
				    		'X-CSRF-TOKEN': _token
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
		    	angular.forEach($scope.provider.sheets, function(item) {
		    		if (item.type == type) {
		    			return item.import;
		    		}
		    	});
		    };

		    $scope.error = function (e) {
		      /* DO SOMETHING WHEN ERROR IS THROWN */
		    	console.log(e);
		    };

		    $scope.returnFile = function() {
		    	$scope.provider = undefined;
		    };

		    $scope.save = function() {
		    	var _token = angular.element(document.querySelector("input[name='_token']")).val();
		    	console.log(_token);

		    	angular.forEach($scope.provider.sheets, function(item) {
		    		angular.forEach(item.table, function(row) {
			    		var _new = {
			    			method: 'POST',
			    			url: $scope.uri+'/detracciones',
			    			headers: {
			    				'X-CSRF-TOKEN': _token
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
			    				row.validated = data.data.insert;
			    			},
			    			function(error) {
			    				console.log(error);
			    			}
			    		);
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
		    	var _export = undefined; 
		    	angular.forEach($scope.provider.sheets, function(item) {
		    		if (item.type == type) {
		    			_export = item;
				    	_export.to_export = "";
				    	var _header = "";
				    	_header += "*"+$scope.provider.ruc+$scope.provider.name.substr(0,35)+$scope.provider.lote+_export.import;
				    	_export.to_export += _header+"\r\n";
				    	angular.forEach(_export.table, function(item) {
				    		var _proform = $scope.leading(item.deposit.proform,9);
				    		var _service = $scope.leading(item.deposit.service,3);
				    		var _account = (type == 'client') ? $scope.leading(item.deposit.account,3) : $scope.leading("0",3);
				    		var _import =  $scope.leading(item.deposit.import,15);
				    		var _operation =  $scope.leading(item.deposit.operation,2);
				    		var _comprobant =  $scope.leading(item.voucher.comprobant,2);
				    		var _number =  (item.voucher.comprobant_number.length > 8) ? item.voucher.comprobant_number.substr(item.voucher.comprobant_number.length-8,item.voucher.comprobant_number.length) : $scope.leading(item.voucher.comprobant_number,8);
				    		var _row = "6"+item.client.number+item.client.name.substr(0,35)+_proform+_service+_account+_import+_operation+item.deposit.period+_comprobant+item.voucher.serie+_number+"\r\n";
				    		_export.to_export += _row;
				    	});
				    	var data = new Blob([_export.to_export], { type: 'text/plain;charset=utf-8' });
				    	var filename = "D"+$scope.provider.ruc+$scope.provider.lote;
				    	FileSaver.saveAs(data,filename+".txt");
		    		}
		    	});
		    };

		    $scope.leading = function(num, size) {
			    var s = num+"";
			    while (s.length < size) s = "0" + s;
			    return s;
			}

			$scope.create_provider = function(){
				var _token = angular.element(document.querySelector("input[name='_token']")).val();
				var new_prov = {
					method: 'POST',
			    	url: $scope.uri+'/provider_add',
			    	headers: {
			    		'X-CSRF-TOKEN': _token
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
						console.log(data);
					},
					function(error){
						
						toastr.error(!jQuery.isEmptyObject(error.data.ruc) ? error.data.ruc : ' ','Mensaje')
						toastr.error(!jQuery.isEmptyObject(error.data.nombre) ? error.data.nombre : ' ','Mensaje')
						toastr.error(!jQuery.isEmptyObject(error.data.cuenta) ? error.data.cuenta : ' ','Mensaje')
						
						console.log(error.data.ruc.length);
						console.log(error);
					}
				)
			}

		}
	])