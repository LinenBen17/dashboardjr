let manifestedGuides = [];

//autofocus manifest_code
$(window).on('load', function () {
    $("input").on("keypress", function () {
        $input = $(this);
        setTimeout(function () {
            $input.val($input.val().toUpperCase());
        }, 50);
    });
    restartFocus();
});

$(document).on('change', '#route_id', function () {
    const route_id = $(this).val();
    const date = $('#date').val();
    const tbody = document.getElementById('tbodyGuidesManifests');
    const tfoot = document.getElementById('tfootGuidesManifests');
    manifestedGuides = [];

    fetch(`/shipment-manifest/buscar-agencia?route_id=${route_id}`)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('agency_destination_id');

            const id = Object.keys(data)[0];
            const value = Object.values(data)[0];

            select.innerHTML = '';
            const option = document.createElement('option');
            option.value = id;
            option.text = value;
            select.appendChild(option);
        })
        .catch(err => {
            console.error('Error al buscar agencia:', err);
        });

    fetch(`/shipment-manifest/crear-manifiesto?route_id=${route_id}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            $('#manifest_code').val(data.manifest_code);

            if (data.manifest_repeat) {
                document.getElementById('manifest_repeat_message').innerText =
                    ' - La ruta seleccionada ya tiene un manifiesto para la fecha indicada.';
            } else {
                document.getElementById('manifest_repeat_message').innerText = '';
            }
        })
        .catch(err => {
            console.error('Error al crear el nuevo codigo de manifiesto:', err);
        });

    fetch(`/shipment-manifest/obtener-guia-manifestadas?route_id=${route_id}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            let totalGuias = data.length;
            let totalPiezas = 0;
            let totalContado = 0;
            let totalPorCobrar = 0;
            let totalCredito = 0;
            let totalPrepago = 0;
            let montoTotal = 0;

            tbody.innerHTML = '';
            data.forEach(guide => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-2 truncate whitespace-nowrap text-center">${guide.mother}</td>
                    <td class="px-4 py-2 truncate whitespace-nowrap">${guide.receiver_name}</td>
                    <td class="px-4 py-2 truncate whitespace-nowrap text-center">${guide.prefix_destination}</td>
                    <td class="px-4 py-2 truncate whitespace-nowrap">${guide.payment_method_name}</td>
                    <td class="px-4 py-2 text-center">${guide.pieces}</td>
                    <td class="px-4 py-2 text-center">${guide.total}</td>
                `;
                tbody.appendChild(tr);

                totalPiezas += parseInt(guide.pieces);
                montoTotal += parseFloat(guide.total);

                if (guide.payment_method_name === 'Contado') {
                    totalContado += parseFloat(guide.total);
                }
                if (guide.payment_method_name === 'Por Cobrar') {
                    totalPorCobrar += parseFloat(guide.total);
                }
                if (guide.payment_method_name === 'Crédito') {
                    totalCredito += parseFloat(guide.total);
                }
                if (guide.payment_method_name === 'Prepago') {
                    totalPrepago += parseFloat(guide.total);
                }

                manifestedGuides.push(guide.id);
            });
            console.log(manifestedGuides);

            tfoot.innerHTML = `
                <tr
                    class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-700 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600">
                    <td class="px-4 py-2 text-left" colspan="3">Guías: &nbsp;<span id="totalGuias">${totalGuias}</span>
                    </td>
                    <td class="px-4 py-2 text-left">Total Piezas:</td>
                    <td class="px-4 py-2 text-center" id="totalPiezas">${totalPiezas}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Contado</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalContado">${totalContado.toFixed(2)}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Por Cobrar</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalPorCobrar">${totalPorCobrar.toFixed(2)}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Crédito</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalCredito">${totalCredito.toFixed(2)}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Prepago</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalPrepago">${totalPrepago.toFixed(2)}</td>
                </tr>
                <tr
                    class="bg-gray-200 dark:bg-gray-700 font-bold text-gray-800 dark:text-gray-100 border-t-2 border-gray-400 dark:border-gray-500">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Total</td>
                    <td></td>
                    <td class="px-4 py-2 text-center text-xl" id="montoTotal">${montoTotal.toFixed(2)}</td>
                </tr>
            `;
        })
        .catch(err => {
            console.error('Error al mostrar guías relacionadas a esa ruta en esa fecha:', err);
        });
});

$('.saveManifest').on('click', function () {
    const manifestCode = $('#manifest_code').val();

    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    Livewire.find(componentId).set(`manifest_code`, $('#manifest_code').val());
    Livewire.find(componentId).set(`date`, $('#date').val());
    Livewire.find(componentId).set(`route_id`, $('#route_id').val());
    Livewire.find(componentId).set(`agency_destination_id`, $('#agency_destination_id').val());
    Livewire.find(componentId).set(`manifested_guides_id`, manifestedGuides);


    window.open(`/shipment_manifest/${manifestCode}/printManifestGuides`, '_blank');

    $('#route_id').prop('selectedIndex', -1);
});

$('.unlinkGuides').on('click', function () {
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    Livewire.find(componentId).set(`manifest_code`, $('#manifest_code').val());
    Livewire.find(componentId).set(`date`, $('#date').val());
    Livewire.find(componentId).set(`route_id`, $('#route_id').val());
    Livewire.find(componentId).set(`agency_destination_id`, $('#agency_destination_id').val());
    Livewire.find(componentId).set(`manifested_guides`, manifestedGuides);

    $('#route_id').prop('selectedIndex', -1);

});

function restartFocus() {
    $("#manifest_code").focus();
}