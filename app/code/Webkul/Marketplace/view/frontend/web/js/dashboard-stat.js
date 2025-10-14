/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    'jquery',
    'uiComponent',
    'ko',
    'chartJs',
    'mage/template',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert',
    'jquery-ui-modules/widget',
    'chartjs/chartjs-adapter-moment',
    'chartjs/es6-shim.min',
    'moment',
    'mage/mage',
    'mage/calendar'
], function ($, Component, ko, Chart, mageTemplate, $t, modal, alert) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Webkul_Marketplace/dashboard-stat',
            orderValueBar: null,
            saleGraph: null,
            orderCountGraph: null,
            backUrl: '',
            opacityLow: '0.4',
            opacityHigh: '1',
            ajaxSuccessMessage: $t('Your mail has been sent.'),
            ajaxErrorMessage: $t('There was error during fetching results.'),
            wrongCaptchaErrorMessage: $t('Wrong verification number.'),
            askQueAdminTitle: $t('Ask Question to Admin'),
            graphColor: '#2F80ED'
        },
        initialize: function () {
            var self = this;
            self._super();
            self.loadingText(self.loading);
            var modalOptions = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: [],
                title: self.askQueAdminTitle
            };
            modal(modalOptions, $(self.askQueAdminContent));
            $(self.askQueToAdmin).on('click', function () {
                $(self.askQueAdminContent).modal("openModal");
            });
            $(self.askQueToAdminSubmit).on('click', function () {
                if ($(self.askQueToAdminForm).valid()) {
                    self.askQueAjax(self);
                }
                
            });
        },
        initObservable: function () {
            this._super().observe(
                'loadingText totalCustomer totalOrder processingOrder completeOrder cancelOrder avgOrderValue customerComparison customerGrowth orderCountComparison orderCountGrowth orderValueComparison orderValueGrowth totalSale saleComparison saleGrowth totalPayout remainingPayout commissionPaid'
            );
            return this;
        },
        openAskQueModal: function(data, event) {
            $(data.askQueAdminContent).modal("openModal");
        },
        initDashboard: function(data, event) {
            var self = this;
            var saleGraph = {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                    label: $t('Total sales'),
                    data: [],
                    fill: false,
                    borderColor: self.graphColor,
                    backgroundColor: self.graphColor,
                    fill: true,
                    tension: 0.1
                }]
                },
                options: {
                    responsive: true,
                    plugins: {
                    legend: { position: 'top', }
                    }
                },
                };
                var orderCountGraph = {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                        label: $t('Total orders'),
                        data: [],
                        fill: false,
                        borderColor: self.graphColor,
                        backgroundColor: self.graphColor,
                        fill: true,
                        tension: 0.1
                    }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                        legend: { position: 'top', }
                        }
                    },
                    };
            self.saleGraph = new Chart("saleGraph", saleGraph);
            self.orderCountGraph = new Chart("orderCountGraph", orderCountGraph);
            self.orderValueBar = new Chart("myChart", {
                type: "bar",
                data: {
                    labels: [],
                    datasets: [{
                    label: $t('Average Order Value'),
                    data: [],
                    backgroundColor: self.graphColor
                    }],
                },
                options: {
                    responsive: true,
                    legend: {display: false}
                }
                });
            $('select.wk-sales-filter>option:eq(4)').attr('selected', true);
            self.filterStat("30day");
            $('#date-range').dateRange({
                dateFormat: 'yyyy-mm-dd',
                from: {
                    id: 'date-from',
                },
                to: {
                    id: 'date-to'
                },
                onClose: function(dateText, inst) {
                    self.filterStat("range");
                }
            });
        },
        formatDate: function(timestamp, id) {
            let day = this.padString(timestamp.getDate());
            let month = this.padString(timestamp.getMonth() + 1);
            let year = timestamp.getFullYear();
            document.getElementById(id).value = `${year}-${month}-${day}`;
        },
        padString: function(date){
            return date.toString().padStart(2, '0');
        },
        askQueAjax: function (currentThis) {
            $('body').trigger('processStart');
            $.ajax({
                url : currentThis.askQueToAdminUrl,
                data : $(currentThis.askQueToAdminForm).serialize(),
                type : 'post',
                dataType : 'json',
                success: function (d) {
                    $('body').trigger('processStop');
                    alert({
                        title: $t('Response'),
                        content: currentThis.ajaxSuccessMessage,
                        actions: {
                            always: function () {
                                $(".wk-contact_input_fields").val("");
                                $(currentThis.askQueAdminContent).modal("closeModal");
                            }
                        }   
                    });
                },
                error: function (response) {
                    $('body').trigger('processStop');
                    alert({
                        content: currentThis.ajaxErrorMessage,
                        actions: {
                            always: function () {
                                $(".wk-contact_input_fields").val("");
                                $(currentThis.askQueAdminContent).modal("closeModal");
                            }
                        }
                    });
                }
            });
        },
        filterStat: function (data, event, isProcess = true) {
            var value = (data == "30day" || data == "range") ? data : event.target.value;
            var self = this;
            if (isProcess) {
                $('body').trigger('processStart');
            }
            const currentDate = new Date();
            let fromDate, toDate , compFrom, compTo;
            $('.wk-daterange-container').css("display","none");
            switch(value) {
                case "today" :
                    self.formatDate(currentDate, 'date-from');
                    self.formatDate(currentDate, 'date-to');
                    compFrom = compTo = new Date(currentDate.getTime() - 1 * 24 * 60 * 60 * 1000);
                    self.formatDate(compFrom, 'compFrom');
                    self.formatDate(compTo, 'compTo');
                    break;
                case "yesterday" :
                    fromDate = toDate = new Date(currentDate.getTime() - 1 * 24 * 60 * 60 * 1000);
                    self.formatDate(fromDate, 'date-from');
                    self.formatDate(toDate, 'date-to');
                    compFrom = compTo = new Date(currentDate.getTime() - 2 * 24 * 60 * 60 * 1000);
                    self.formatDate(compFrom, 'compFrom');
                    self.formatDate(compTo, 'compTo');
                    break;
                case "7day" :
                    fromDate = new Date(currentDate.getTime() - 6 * 24 * 60 * 60 * 1000);
                    toDate = new Date(currentDate.getTime());
                    self.formatDate(fromDate, 'date-from');
                    self.formatDate(toDate, 'date-to');
                    compFrom = new Date(currentDate.getTime() - 13 * 24 * 60 * 60 * 1000);
                    compTo = new Date(currentDate.getTime() - 7 * 24 * 60 * 60 * 1000);
                    self.formatDate(compFrom, 'compFrom');
                    self.formatDate(compTo, 'compTo');
                    break;
                case "30day" :
                    fromDate = new Date(currentDate.getTime() - 29 * 24 * 60 * 60 * 1000);
                    toDate = new Date(currentDate.getTime());
                    self.formatDate(fromDate, 'date-from');
                    self.formatDate(toDate, 'date-to');
                    compFrom = new Date(currentDate.getTime() - 59 * 24 * 60 * 60 * 1000);
                    compTo = new Date(currentDate.getTime() - 30 * 24 * 60 * 60 * 1000);
                    self.formatDate(compFrom, 'compFrom');
                    self.formatDate(compTo, 'compTo');
                    break;
                case "lastmonth" :
                    fromDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
                    toDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
                    self.formatDate(fromDate, 'date-from');
                    self.formatDate(toDate, 'date-to');
                    compFrom = new Date(currentDate.getFullYear(), currentDate.getMonth() - 2, 1);
                    compTo = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 0);
                    self.formatDate(compFrom, 'compFrom');
                    self.formatDate(compTo, 'compTo');
                    break;
                case "lifetime" :
                    const lifeTimeFrom = new Date("2001", "0", "1");
                    self.formatDate(lifeTimeFrom, 'date-from');
                    self.formatDate(currentDate, 'date-to');
                    break;
                case "range" :
                    $('.wk-daterange-container').css("display","flex");
                    break;

            }
            $.ajax({
                url: self.statUrl,
                data: {
                    dateFrom: $("#date-from").val(),
                    dateTo: $("#date-to").val(),
                    compFrom: $("#compFrom").val(),
                    compTo: $("#compTo").val(),
                    filterType: $(".wk-sales-filter").val()
                },
                type:'post',
                dataType:'json',
                success:function(response) {
                    response = response.data;
                    self.totalOrder(response.orderStat.all);
                    self.processingOrder(response.orderStat.processing);
                    self.completeOrder(response.orderStat.complete);
                    self.cancelOrder(response.orderStat.cancel);
                    self.totalCustomer(response.topCustomer.length);
                    self.avgOrderValue(response.orderValueData.avgOrderValueFormatted);
                    self.totalSale(response.orderSaleData.totalSale);
                    self.totalPayout(response.orderSaleData.totalPayout);
                    self.remainingPayout(response.orderSaleData.remainingPayout);
                    self.commissionPaid(response.orderSaleData.commissionPaid);
                    self.saleComparison(response.saleComparison);
                    self.saleGrowth(response.saleComparison.toFixed(2) + "%");
                    self.customerComparison(response.customerComparison);
                    self.customerGrowth(response.customerComparison.toFixed(2) + "%");
                    self.orderCountComparison(response.orderCountComparison);
                    self.orderCountGrowth(response.orderCountComparison.toFixed(2) + "%");
                    self.orderValueComparison(response.orderValueComparison);
                    self.orderValueGrowth(response.orderValueComparison.toFixed(2) + "%");

                    self.saleGraph.data.datasets[0].data = response.orderSaleData.graphData;
                    self.saleGraph.data.labels = response.orderSaleData.graphXValue;
                    self.saleGraph.update();

                    self.orderCountGraph.data.datasets[0].data = response.orderGraphData.graphData;
                    self.orderCountGraph.data.labels = response.orderGraphData.graphXValue;
                    self.orderCountGraph.update();

                    self.orderValueBar.data.datasets[0].data = response.orderValueData.graphData;
                    self.orderValueBar.data.labels = response.orderValueData.graphXValue;
                    self.orderValueBar.update();


                    var jsTemplate = mageTemplate('#wk-top-customer-template'),tmpl;
                    tmpl = jsTemplate({
                        data: response
                    });
                    $('.wk-top-customer').html(tmpl);
                    jsTemplate = mageTemplate('#wk-top-product-template');
                    tmpl = jsTemplate({
                        data: response
                    });
                    $('.wk-top-product').html(tmpl);
                    jsTemplate = mageTemplate('#wk-top-category-template');
                    tmpl = jsTemplate({
                        data: response
                    });
                    $('.wk-top-category').html(tmpl);
                    if (isProcess) {
                        $('body').trigger('processStop');
                    }
                }
            });
        }
    });
}
);
