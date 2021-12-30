<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    $this->setFrameMode(true);
    $LOAN_AMOUNT = (int)$_REQUEST['LOAN_AMOUNT'];
    $LOAN_LENGTH = (int)$_REQUEST['LOAN_LENGTH'];
    $LOAN_PERCENT = htmlspecialchars($_REQUEST['LOAN_PERCENT'])/100;
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    $this->setFrameMode(true);
    $LOAN_AMOUNT = (int)$_REQUEST['LOAN_AMOUNT'];
    $LOAN_LENGTH = (int)$_REQUEST['LOAN_LENGTH'];
    $ONE = (int)$_REQUEST['one'];
    $not_valid = (int)$_REQUEST['notValid'];
    $LOAN_PERCENT = htmlspecialchars($_REQUEST['LOAN_PERCENT'])/100;
    $percent = (float)$_REQUEST['percent'];
    $collateral_type_id = (int)$_REQUEST['collateralTypeId'];
    if($collateral_type_id==1){
        $insurancePercent = 60;
    }
    else{
        $insurancePercent = 0;
    }
?>

<?php
    // $LOAN_AMOUNT = (int)345000000;
    //$LOAN_LENGTH = (int)120;
    // $LOAN_PERCENT = (float)0.18;
    function PMT($interest,$num_of_payments,$PV,$FV = 0.00, $Type = 0){
        $xp=pow((1+$interest),$num_of_payments);
        return
            ($PV* $interest*$xp/($xp-1)+$interest/($xp-1)*$FV)*
            ($Type==0 ? 1 : 1/($interest+1));
    }
    // echo($LOAN_AMOUNT.' - '.$LOAN_LENGTH.' - '.$LOAN_PERCENT);

	$insurance_cost = $LOAN_AMOUNT * $insurancePercent/100;
    // echo('<br>Сумма кр - Insurance const: '.$insurance_cost);
    $insurance_expenses = $insurance_cost *$percent/1200*$LOAN_LENGTH;
    // echo('<br><br>расходы на страховку залога - insurance_expenses: '.$insurance_expenses);
    $E13 = 570000;
    $E14 = 337500;
    if($insurance_cost>=570000*5000){
        $PROJECT_PREPARATION_SUM = 2 * 570000;
    }
    else{
        $PROJECT_PREPARATION_SUM = $insurance_cost>=570000000?$E14:$E13;//=ЕСЛИ(B13>=223000000;E14;E13);
    }
    $PROJECT_PREPARATION_SUM_FORMAT = number_format($PROJECT_PREPARATION_SUM, 2, '.', ' ');
    // echo('<br>PREPARATION SUM: '.$PROJECT_PREPARATION_SUM_FORMAT);
    $other_paid_services = 570000*0.3;

	if($collateral_type_id!=1){
		$payment_amount = -$LOAN_AMOUNT; 
	}
	else {
		$payment_amount = -$LOAN_AMOUNT + $insurance_expenses;// + $PROJECT_PREPARATION_SUM+$other_paid_services;
	}

    $payment_amount_format = number_format($payment_amount, 2, '.', ' ');
    // echo('<br>Сумма платежа: '.$payment_amount_format);
    // echo('<br>');
    $payment_date = date('m/d/Y', time());
    //$tmp  = explode("/", $date);
    //echo($tmp[0].' '.$tmp[1].' '.$tmp[2]);
    $prev_timestamp = time();

    $date = new DateTime("@$prev_timestamp");
    // echo('Дата платежа: ');
    // echo $date->format('U = d.m.Y') . "<br>\n";
    $nextMonth = time();
    $prev_date = $date;
    $c_prev = +$LOAN_AMOUNT;
    $d_prev = 0;
    $itogo_d = 0;
    $itogo_e = 0;
    $itogo_f = 0;
    $XIRR_values = array();
    $XIRR_dates = array();
    array_push($XIRR_values, $payment_amount);
    array_push($XIRR_dates, $date->getTimestamp());

?>
<!--<h4><?=GetMessage('CREDIT_AMOUNT')?></h4>-->
<div class="modal fade" id="myModalCreditDetail" tabindex="-1" role="dialog" aria-labelledby="myModalCreditDetailLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalCreditDetailLabel"><?=GetMessage('PC_CALCULATION_OF_THE_LOAN')?></h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th><?=GetMessage('PC_DATE')?></th>
                            <th><?=GetMessage('PC_CREDIT_BALANCE')?></th>
                            <th><?=GetMessage('PC_AMOUNT_LOAN_REPAYMENT')?></th>
                            <th><?=GetMessage('PC_AMOUNT_INTEREST_LOAN')?></th>
                            <th><?=GetMessage('PC_TOTAL_TO_MATURITY')?></th>
                            <th><?=GetMessage('PC_HOW_MANY_DAYS_MONTH')?></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for($i = 1; $i<=$LOAN_LENGTH;$i++){
                                // $nextMonth = mktime(0, 0, 0, date("m"), date("d"),   date("Y"));
                                $date = new DateTime("@$nextMonth");
                                // echo $date->format('Y-m-d') . "<br>\n";

                                //if($date->format('d')<15){
                                    date_date_set($date,
                                    date_format($date, 'Y'),
                                    $date->format('m')+$i,
                                    $date->format('d')-1);
                                    //}
                                    //else
                                    //date_date_set($date,
                                    //date_format($date, 'Y'),
                                    //$date->format('m')+$i,
                                    //15);
                                // $credit_date = ($date->format('d-m-Y')) - ($prev_date->format('d-m-Y'));
                                $credit_date  = round((strtotime ($date->format('d.m.Y'))-strtotime ($prev_date->format('d.m.Y')))/(60*60*24));
                                // $credit_date = 29;
                                $c23 = $c_prev - $d_prev;

                                $e23 = abs($c23)*$LOAN_PERCENT/365*$credit_date;
                                $f23 = abs($e23);

                                $iMonthlyPayment = ceil(abs(PMT(($LOAN_PERCENT/12), $LOAN_LENGTH - 1, (-1)*$LOAN_AMOUNT))/1000)*1000;
                                $iMonthlyPaymentFormat = number_format($iMonthlyPayment, 2, '.', ' ');
                                // $d_prev = abs($iMonthlyPayment) - $e23;

                                if($i==$LOAN_LENGTH){
                                    $d23 = $c23;
                                    $f23 = abs($d23) + $e23;
                                }
                                else{
                                    if($i==1){
                                        $d23 = 0;
                                        $f23 = $e23;
                                    }
                                    else{
                                        $f23 = $iMonthlyPayment;
                                        $d23 = abs($f23) - $e23;
                                    }

                                }

                                $c_prev = $c23;
                                $d_prev = $d23;
                                $itogo_d = $itogo_d + $d23;
                                $itogo_e = $itogo_e + $e23;
                                $itogo_f = $itogo_f + $f23;

                                // echo $date->format('d.m.Y') .' -prev:  '. $prev_date->format('d.m.Y').' - '.$credit_date ."<br>\n";
                                // echo $date->format('d.m.Y') .' => '. number_format($c23,2,'.',' '). ' => '. number_format($d23,2,'.',' '). ' => '. number_format($e23,2,'.',' ').' => '.number_format($f23,2,'.',' '). ' => ' .$credit_date."<br>\n";

                                $prev_date = $date;


                                // echo $date->format('d.m.Y') .' -prev:  '. $prev_date->format('d.m.Y').' - '.$credit_date ."<br>\n";
                                // echo $date->format('d.m.Y') .' => '. number_format($c23,2,'.',' '). ' => '. number_format($d23,2,'.',' '). ' => '. number_format($e23,2,'.',' ').' => '.number_format($f23,2,'.',' '). ' => ' .$credit_date."<br>\n";

                                $prev_date = $date;

                                array_push($XIRR_values, $f23);
                                array_push($XIRR_dates, $date->getTimestamp());
                                echo('
                                    <tr>
                                    <td>'.$i.'</td>
                                    <td>'.$date->format('d.m.Y').'</td>
                                    <td>'.number_format($c23,2,'.',' ').'</td>
                                    <td>'.number_format($d23,2,'.',' ').'</td>
                                    <td>'.number_format($e23,2,'.',' ').'</td>
                                    <td>'.number_format($f23,2,'.',' ').'</td>
                                    <td>'.$credit_date.'</td>
                                    </tr>
                                ');


                            }
                            // echo '<br>';
                            // echo('Итого D: '.number_format($itogo_d,2,'.',' ').' - Итого E: '.number_format($itogo_e,2,'.',' ').' - Итого F: '.number_format($itogo_f, 2, '.',' '));

                            // var_dump($XIRR_values);
                            // echo '<br>';
                            // var_dump($XIRR_dates);
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><?=GetMessage('PC_TOTAL')?></td>
                            <td><?=number_format($itogo_d,2,'.',' ')?></td>
                            <td id="money"><?=number_format($itogo_e,2,'.',' ')?></td>
                            <td><?=number_format($itogo_f, 2, '.',' ')?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <?php
                    $f = new Financial;
                    $XIRR = $f->XIRR($XIRR_values, $XIRR_dates, 0.1);
                    $XIRR_FORMAT = number_format(floatval($XIRR * 100),1,'.',' ');
                ?>
                <p style="display:none"><?php var_dump($XIRR_values);?></p>
                <p style="display: none"><?php var_dump($XIRR_dates); ?></p>

                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td><?=GetMessage('PC_PSK_PERCENT')?></td>
                            <td id="psk"><?=$XIRR_FORMAT?>%</td>
                        </tr>
                        <tr>
                            <td><?=GetMessage('PC_PAYMENTS_ON_PRINCIPAL')?></td>
                            <td><?=number_format($itogo_f, 2, '.',' ')?></td>
                        </tr>
                        <tr>
                            <td><?=GetMessage('INSURANCE_COSTS')?></td>
                            <td><?=number_format($insurance_expenses,2,'.',' ')?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs pull-left" data-dismiss="modal"><?=GetMessage('PC_CLOSE')?></button>
                <?/*<button type="button" class="btn btn-primary btn-lg _js-btn-popup-order-form" data-toggle="modal" data-target="#myModalCreditOrder"><?=GetMessage('PC_SEND_APPLICATION')?> <i class="la la-arrow-right"></i></button>*/?>
                <button class="btn btn-primary btn-lg js-btn-order-credit-child" href="#"><?=GetMessage('PC_SEND_APPLICATION')?> <i class="la la-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    if(<?=$ONE?> == 0){
        var url_string = window.location.href
        var lang = url_string.split('/')[3];
        var langData = new Map ([
            [ 'ru' , {
                realEstVal: "Стоимость Автомобиля",
                initialFee: "Первоначальный взнос",
                creditAmount: "Сумма кредита",
                loanInterestRate: "Процентная ставка по кредиту %",
                loanRepaymentMethod: "Метод погашения кредита",
                collateralType: "Вид обеспечения",
                calcText: 'Рассчитайте ваш кредит',
                creditTerm: "Срок кредита",
                acquiredProperty: "Залог приобретаемого транспортного средства + полис риска непогашения кредита",
                annuity: "Аннуитетный",
                borrowerPayments: "Платежи заемщика связанные с получением кредита",
                propertyValuationCost: "Стоимость оценки имущества",
                notValid: "не действующее",
                mortgageInsuranceRate: "% ставка по страхованию залога",
                insurancePremium: "Сумма страховой премии по страхованию залога",
                calculate: "Рассчитать",
                text: "                        Расчет является предварительным. Точные условия по кредиту вам будут предоставлены в отделении банка.                                                    Полная стоимость потребительского кредита рассчитана в соответствии с законодательством Республики Узбекистан и включает в себе следующее (в расчет ПСК могут быть включены все или отдельные виды нижеследующих платежей):\
                Платежи по основному долгу и процентам;\
                Платежи на страхование риска непогашения кредита;\
                Платежи на страхование залога;\
                Платежи в пользу бюджетных организаций;\
                Полная стоимость кредита (ПСК) должна отражаться на сайте в %.                                            ",

                loanCalc: "Расчет по кредиту",
                date: "Дата",
                loanBalance: "Остаток кредита",
                loanRepaymentAmount : "Сумма погашения кредита",
                loanInterestsAmount: "Сумма процентов по кредиту",
                totalForRepayment: "Итого к погашению",
                days: "Сколько дней в месяце",
                close: "Закрыть",
                make: "Отправить заявку",
                ltc: "ПСК в %",
                paymentsOnPrincipal: "Платежи по основному долгу и процентам",
                stateDuty: "Гос.пошлина за удостоверение залога",
                insuranceExpenses: "Сумма расходов по страхованию",
                otherPaid: "Другие платные услуги нотариуса",
                realEstatePledge: "Залог недвижимого имущества",
                pledgeOfCars: "Залог легковых автотранспортных средств",
                cashDeposit: "депозит денежных средств",
                model: "Модель",
                makeRequest: "Оформить заявку",

            }],
            [ 'uz' , {
                realEstVal: "Avtomobil narxi;",
                initialFee: "Boshlang'ich badal",
                creditAmount: "Kredit summasi",
                loanInterestRate: "Kreditning foiz stavkasi%",
                loanRepaymentMethod: "Kreditni qaytarish usuli",
                collateralType: "Ta'minot turi",
                calcText: 'Kreditingizni hisoblang',
                creditTerm: "Kredit muddati",
                acquiredProperty: "Sotib olingan transport vositasining garovi + kreditni to'lay olmaslik xavfi polisi;",
                annuity: "Annuitet",
                borrowerPayments: "Qarz oluvchining kredit olish bilan bog'liq to'lovlari",
                propertyValuationCost: "Mulkni baholash qiymati",
                notValid: "-",
                mortgageInsuranceRate: "% ipoteka sug'urta stavkasi",
                insurancePremium: "Garovni sug'urtalash uchun sug'urta mukofotining miqdori",
                calculate: "Hisoblash",
                text: "Hisob-kitoblar taxminiy. Kredit bo’yicha yanada batafsil ma’lumot bank bo’limida taqdim etiladi.",

                loanCalc: "Kredit bo’yicha hisob-kitob",
                date: "Sana",
                loanBalance: "Kredit qoldig’i",
                loanRepaymentAmount : "Kredit to’lash summasi",
                loanInterestsAmount: "Kredit bo’yicha foizlar summasi",
                totalForRepayment: "Jami to’lanadigan summa",
                days: "Bir oyda necha kun",
                close: "Yopish",
                make: "Talabnoma yuborish",
                ltc: "KTQ foizlarda",
                paymentsOnPrincipal: "Asosiy qarz va foizlar boʻyicha toʻlovlar",
                stateDuty: "Garovni sertifikatlash uchun davlat boji",
                insuranceExpenses: "Sug'urta xarajatlari summasi",
                otherPaid: "Boshqa pullik notarial xizmatlar",
                realEstatePledge: "Ko'chmas mulk garovi",
                pledgeOfCars: "Залог легковых автотранспортных средств",
                cashDeposit: "депозит денежных средств",
                pledgeOfCars: "Yengil avtotransport vositalarining garovi; ",
                cashDeposit: "pul mablag'lari depoziti",
                model: "Model",
                makeRequest: "Talabnoma yuborish"
            }],
            [ 'en' , {
                realEstVal: "Cost of a vehicle;",
                initialFee: "Initial contribution",
                creditAmount: "Amount of credit",
                loanInterestRate: "Loan interest rate%",
                loanRepaymentMethod: "Loan repayment method",
                calcText: 'Calculate your loan',
                collateralType: "Type of collateral",
                creditTerm: "Credit term",
                acquiredProperty: "Collateral of a purchased vehicle + policy of a risk of non-repaying loan;",
                annuity: "Annuity",
                borrowerPayments: "Borrower payments related to obtaining a loan",
                propertyValuationCost: "Property Valuation Cost",
                notValid: "not valid",
                mortgageInsuranceRate: "% mortgage insurance rate",
                insurancePremium: "Amount of insurance premium for pledge insurance",
                calculate: "Calculate",
                text: "The calculation is preliminary. You will be provided with exact loan terms and conditions in one of the branches of the bank.",

                loanCalc: "Loan calculation",
                date: "Date",
                loanBalance: "Loan balance",
                loanRepaymentAmount : "Loan repayment amount",
                loanInterestsAmount: "Loan interests amount",
                totalForRepayment: "Total for repayment",
                days: "How many days are there in a month",
                close: "Close",
                make: "Make an enquiry",
                ltc: "LTC in %",
                paymentsOnPrincipal: "Payments on principal and interest",
                stateDuty: "State duty for certification of pledge",
                insuranceExpenses: "Sum of insurance expenses",
                otherPaid: "Other paid notary services",
                realEstatePledge: "Real estate pledge",
                pledgeOfCars: "Collateral of motor vehicles",
                cashDeposit: "Deposit of funds",
                model: "Model",
                makeRequest: "Make a request"
            }]
        ]);

        $('.item-calc').empty();
        $('.js-btn-order-credit').text(langData.get(lang).makeRequest).attr('href', 'https://avto.kapitalbank.uz');
        $('<form>',{
                id: '',
                method: 'post',
                enctype: 'multipart/form-data',
                role: 'form',
            }).appendTo('.item-calc').append('\
                <input type="hidden" name="MODDEL" value>\
                <input type="hidden" name="dev" value>\
                <input type="hidden" name="CALC_TYPE" value="DEFAULT">\
                <h2>'+langData.get(lang).calcText+'</h2>\
                <div class="form-group form-group-lg is-empty">\
                <label for="form-field-1">'+langData.get(lang).realEstVal+':</label>\
                <span class="help-block"></span>\
                <div class="row">\
                    <div class="col-sm-9">\
                        <input type="text" id="loan_amount_id" class="js-range-slider" name="my_range" value="" />\
                    </div>\
                    <div class="col-sm-3">\
                        <input type="text" id="loan_amount_input" name="LOAN_AMOUNT" class="form-control">\
                    </div>\
                </div>\
                </div>\
                <div class="form-group form-group-lg is-empty">\
                <label for="form-field-1">'+langData.get(lang).initialFee+':</label>\
                <span class="help-block"></span>\
                <div class="row">\
                    <div class="col-sm-9">\
                        <input type="text" id="initial_fee_id" class="js-range-slider" name="my_range" value="" />\
                    </div>\
                    <div class="col-sm-3">\
                        <input type="text" id="initial_fee_input" name="initial_fee" class="form-control">\
                    </div>\
                </div>\
                </div>\
                <div class="form-group form-group-lg">\
                    <label for="form-field-4" class="h4">'+langData.get(lang).creditAmount+':</label>\
                    <!--<p class="help-block"></p>-->\
                    <input type="text" id="amount_of_credit_input" name="initial_fee" class="form-control" readonly="">\
                </div>\
                <div class="row">\
                <div class="col-sm-6">\
                    <div class="form-group form-group-lg">\
                        <label for="form-field-4" class="h4">'+langData.get(lang).loanInterestRate+':</label>\
                        <!--<p class="help-block"></p>-->\
                        <input type="text" name="LOAN_PERCENT" id="loan_percent_input" class="form-control" value="23.00" readonly="">\
                    </div>\
                </div>\
                <div class="col-sm-6">\
                    <div class="form-group form-group-lg">\
                        <label for="form-field-4" class="h4">'+langData.get(lang).loanRepaymentMethod+':</label>\
                        <input type="text" name="LOAN_PERCENT"  class="form-control" value="'+langData.get(lang).annuity+'" readonly="">\
                    </div>\
                </div>\
                <div class="col-sm-6">\
                    <div class="form-group form-group-lg">\
                        <label for="form-field-4" class="h4">'+langData.get(lang).collateralType+':</label>\
                        <select id="collateralTypeId" class="form-control">\
                            <option value="1">'+langData.get(lang).acquiredProperty+'</option>\
                            <option value="2">'+langData.get(lang).cashDeposit+'</option>\
                        </select>\
                    </div>\
                </div>\
                </div>\
                <div class="form-group form-group-lg is-empty">\
                <label for="form-field-1">'+langData.get(lang).creditTerm+':</label>\
                <span class="help-block"></span>\
                <div class="row">\
                    <div class="col-sm-9">\
                        <input type="text" id="loan_length_id" class="js-range-slider" name="my_range" value="" />\
                    </div>\
                    <div class="col-sm-3">\
                        <input type="text" id="loan_length_input" name="initial_input" class="form-control">\
                    </div>\
                </div>\
                </div>\
                <label for="form-field-4 form-group form-group-lg" class="h4">'+langData.get(lang).borrowerPayments+':</label>\
                <div class="row">\
                <!--<div class="col-sm-6">\
                    <div class="form-group form-group-lg">\
                        <label for="form-field-4" class="h4">'+langData.get(lang).propertyValuationCost+':</label>\
                        <input type="text" name="LOAN_PERCENT" id="notValid"  class="form-control" value="'+langData.get(lang).notValid+'" readonly="">\
                    </div>\
                </div>-->\
                <div class="col-sm-5">\
                    <div class="form-group form-group-lg">\
                        <label for="form-field-4" class="h4">'+langData.get(lang).mortgageInsuranceRate+':</label>\
                        <input type="text" name="LOAN_PERCENT" id="mortgageInsuranceRate"  class="form-control" value="1" >\
                    </div>\
                </div>\
                <div class="col-sm-7">\
                    <div class="form-group form-group-lg">\
                        <label for="form-field-4" class="h4">'+langData.get(lang).insurancePremium+':</label>\
                        <input type="text" id="insurance_percent" name="insurance_percent"  class="form-control" value="" readonly="">\
                    </div>\
                </div>\
                </div>\
                <div class="text-center" style="margin: 16px;">\
                <button style="padding:10px 16px; width: 255px;" id="calc" type="button" class="btn btn-primary _btn-primary-2 _btn-primary-2-effect btn-lg _btn-lg-fix _btn-color-1 _btn-text-up jq-btn-calc-credit">'+langData.get(lang).calculate+'</button>\
                </div>\
                <div class="pc-alert pc-alert-info">\
                            <i class="item-icon sprite-icon sprite-icon-info-blue"></i>\
                            <i id="preview-click" class="item-icon-2 sprite-icon sprite-icon-arrows-top-down btn-collapser"></i>\
                            <div class="preview-text-collapser pc-alert-collapser hide-class"><div id="preview-text" style="overflow: hidden; height: 36px;">\
                '+langData.get(lang).text+'\
                            </div></div>\
                </div>\
                <input type="hidden" id="one" value="1">\
                <div id="pdf-box" style="margin: 15px 0 0;"></div>\
                <div class="calc-result jq-calc-result">\
                </div>\
            ')

            var tmp = true;
            $('#preview-click').on('click', function(){
                if(tmp){
                    var height = 'auto';
                    tmp = false;
                }
                else{
                    var height = '36px';
                    tmp = true;
                }

                $('#preview-text').css({ 'height': height });
            });

            var $range = $("#loan_amount_id");
            var $input = $("#loan_amount_input");
            var $range_fee = $("#initial_fee_id");
            var $input_fee = $("#initial_fee_input");
            var $range_length = $("#loan_length_id");
            var $input_length = $("#loan_length_input");
            var $input_insurance_percent = $('#insurance_percent');
            var $input_amount_of_credit = $('#amount_of_credit_input');

            var instance;
            var instance_fee;
            var instance_length;

            var min = 5000000;
            var max = 270000000;
            var min_fee = 1000000;
            var max_fee = 700000000;

            var min_length = 12;
            var max_length = 60;

            $('#collateralTypeId').change(function(){
                if(parseInt($(this).val())==1){
                    $('#notValid').val(langData.get(lang).notValid).attr('readonly', '');
                    $('#insurance_percent').val((parseInt($input_amount_of_credit.val().replace(/\s/g, '')) * 1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*$input_length.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2})).removeAttr('readonly')
                    .attr('readonly', '');
                }
                else if(parseInt($(this).val())==2){
                    // $('#mortgageInsuranceRate').val('-').attr('readonly', '');
                    $('#insurance_percent').val('-').attr('readonly', '');

                }
            });

            $range.ionRangeSlider({
                skin: "round",
                type: "single",
                step: 1,
                min: min,
                max: max,
                from: 50000000,
                grid: true,
                onStart: function(data) {
                    $input.prop("value", data.from);
                    min_fee = Math.round(data.from * 0.6);

                    // instance_fee.update({
                    //     min: data.from*0.3
                    // });
                },
                onChange: function(data) {
                    $input.prop("value", data.from);

                    if($('#model').val()==1){
                        instance_fee.update({
                            min: Math.round(data.from*0.6)
                        });
                    }
                    else{
                        instance_fee.update({
                            min: Math.round(data.from*0.6)
                        });
                    }

                    if(data.from<$input_fee.val()){
                        instance_fee.update({
                            from: data.from
                        });
                        $input_fee.prop("value", data.from);
                    }
                    else if(data.from>parseInt($input_fee.val())+300000000){
                        //console.log('test')
                        instance_fee.update({
                            from: data.from - 300000000
                        });
                        $input_fee.prop("value", data.from - 300000000);
                    }

                    if($('#model').val()==1){
                        min_fee = Math.round(data.from * 0.6);
                    }
                    else{
                        min_fee = Math.round(data.from * 0.6);
                    }
                    $input_amount_of_credit.prop("value", (data.from - $input_fee.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))

                    if($('#collateralTypeId').val()==1)
                        $input_insurance_percent.prop('value', Math.round((data.from - $input_fee.val())*1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*$input_length.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))
                    $('.js-monthly-payment').html('...');
                    $('.modal-body').empty()
                    $('#pdf').remove();
                }
            });

            instance = $range.data("ionRangeSlider");

            $('#mortgageInsuranceRate').on("input", function(){
                var val = $(this).prop("value");
                if($('#collateralTypeId').val()==1)
                    $input_insurance_percent.prop('value', Math.round(($input_amount_of_credit.val().replace(/\s/g, ''))*1.2*val/1200*$input_length.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))
            });

            $input.on("input", function() {
                var val = $(this).prop("value");

                // validate
                if (val < min) {
                    val = min;
                } else if (val > max) {
                    val = max;
                }

                instance.update({
                    from: val
                });

                if($('#model').val()==1){
                    instance_fee.update({
                        min: Math.round(val*0.6)
                    });
                    min_fee = Math.round(val* 0.6);
                }
                else{
                    instance_fee.update({
                        min: Math.round(val*0.6)
                    });
                    min_fee = Math.round(val* 0.6);
                }

                // //console.log(val, ' - ', $input_fee.val())
                if(parseInt(val)<parseInt($input_fee.val())){
                    instance_fee.update({
                        from: val
                    });
                    $input_fee.prop("value", val);
                }
                else if(parseInt(val)>parseInt($input_fee.val())+300000000){
                    //console.log('test')
                    instance_fee.update({
                        from: parseInt(val) - 300000000
                    });
                    $input_fee.prop("value", parseInt(val) - 300000000);
                }else{
                    if($('#model').val()==1){
                        $input_fee.prop("value", parseInt(val*0.6));
                    }
                    else{
                        $input_fee.prop("value", parseInt(val*0.6));
                    }
                }

                $input_amount_of_credit.prop("value", (val -  $input_fee.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}));
                if($('#collateralTypeId').val()==1)
                    $input_insurance_percent.prop('value', Math.round((val - $input_fee.val())*1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*$input_length.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))

                $('.js-monthly-payment').html('...');
                $('.modal-body').empty()
                $('#pdf').remove();
            });

            $range_fee.ionRangeSlider({
                skin: "round",
                type: "single",
                min: min_fee,
                max: max_fee,
                from: 50000000,
                grid: true,
                onStart: function(data) {
                    $input_fee.prop("value", data.from);
                },
                onChange: function(data) {
                    $input_fee.prop("value", data.from);
                    if(parseInt(data.from)>parseInt($input.val())){
                        instance.update({
                            from: data.from
                        });
                        $input.prop("value", data.from);
                    }
                    else if(parseInt(data.from)<parseInt($input.val())-300000000){
                        //console.log('test')
                        instance.update({
                            from: parseInt(data.from) + 300000000
                        });
                        $input.prop("value", parseInt(data.from) + 300000000);
                    }
                    $input_amount_of_credit.prop("value", ($input.val() - data.from).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}));
                    if($('#collateralTypeId').val()==1)
                        $input_insurance_percent.prop('value', Math.round(($input.val() - data.from)*1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*$input_length.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))
                    $('.js-monthly-payment').html('...');
                    $('.modal-body').empty()
                    $('#pdf').remove();
                }
            });

            $input_amount_of_credit.prop("value", ($input.val() -  $input_fee.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}));

            if($('#collateralTypeId').val()==1)
                $input_insurance_percent.prop('value', Math.round(($input.val() - $input_fee.val())*1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*$input_length.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))

            $('.js-monthly-payment').html('...');
            $('.modal-body').empty()
            $('#pdf').remove();
            instance_fee = $range_fee.data("ionRangeSlider");

            $input_fee.on("input", function() {
                var val = $(this).prop("value");
                //console.log(val, ' - ', )

                // validate
                instance_fee.update({
                    from: val
                });

                //console.log(val, ' - ', )
                if(parseInt(val)>parseInt($input.val())){
                    instance.update({
                        from: val
                    });
                    $input.prop("value", val);
                }
                else if(parseInt(val)<parseInt($input.val())-300000000){
                    //console.log('test')
                    instance.update({
                        from: parseInt(val) + 300000000
                    });
                    $input.prop("value", parseInt(val) + 300000000);
                }

                $input_amount_of_credit.prop("value", ($input.val() - val).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}));
                if($('#collateralTypeId').val()==1)
                    $input_insurance_percent.prop('value', Math.round(($input.val() - val)*1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*$input_length.val()).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))

                $('.js-monthly-payment').html('...');
                $('.modal-body').empty()
                $('#pdf').remove();
            });


            $input_amount_of_credit.on("input", function() {
                var val = $(this).prop("value");
                //console.log('test')

                // validate
                if (val < min) {
                    val = min;
                } else if (val > max) {
                    val = max;
                }
            });


            $range_length.ionRangeSlider({
                skin: "round",
                type: "single",
                min: min_length,
                max: max_length,
                from: 36,
                grid: true,
                onStart: function(data) {
                    $input_length.prop("value", data.from);
                },
                onChange: function(data) {
                    $input_length.prop("value", data.from);
                    if($('#collateralTypeId').val()==1)
                        $input_insurance_percent.prop('value', Math.round(($input.val() - $input_fee.val())*1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*data.from).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))
                    $('.js-monthly-payment').html('...');
                    $('#pdf').remove();
                    $('.modal-body').empty()
                }
            });

            instance_length = $range_length.data("ionRangeSlider");

            $input_length.on("input", function() {
                var val = $(this).prop("value");

                // validate
                if (val < min_length) {
                val = min_length;
                } else if (val > max_length) {
                val = max_length;
                }

                instance_length.update({
                from: val
                });
                if($('#collateralTypeId').val()==1)
                    $input_insurance_percent.prop('value', Math.round(($input.val() - $input_fee.val())*1.2*parseFloat($('#mortgageInsuranceRate').val())/1200*val).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}))
                $('.js-monthly-payment').html('...');
                $('#pdf').remove();
                $('.modal-body').empty()
            });

            $('#model').change(function(){
                if(parseInt($(this).val())==1){
                    instance_fee.update({
                        min: Math.round($input.val()*0.6)
                    });
                    if($input_fee.val()<$input.val()*0.6){
                        $input_fee.val($input.val()*0.6);
                        $('#amount_of_credit_input').val($input.val() - $input.val()*0.6).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2})
                    }
                    instance_length.update({
                        max: 40
                    });
                }
                else if(parseInt($(this).val())==2){
                    instance_fee.update({
                        min: Math.round($input.val()*0.6),
                    });
                    instance_length.update({
                        max: 36,
                        from: 36
                    });
                    $input_length.val(36)
                }
                else{
                    instance_fee.update({
                        min: Math.round($input.val()*0.6)
                    });
                    instance_length.update({
                        max: 36,
                        from: 36
                    });
                    $input_length.val(36)

                }
            });

            $('#calc').on('click', function(){
                $('#myModalCreditOrder').attr({
                    'data-amount': $('#loan_amount_input').val(),
                    'data-term' : $('#loan_length_input').val(),
                    'data-percent': $('#loan_percent_input').val(),
                });
                $('.jq-calc-result').empty();
                var notValid;

                if(parseInt($('#notValid').val())){
                notValid = parseInt($('#notValid').val());
            }

            $.ajax({
                type: "GET",
                url: "/ru/crediting/calc_auto.php",
                // CALC_TYPE=AUTO&LOAN_AMOUNT_RANGE=137000000&LOAN_AMOUNT=137000000&LOAN_LENGTH_RANGE=8&LOAN_LENGTH=8&LOAN_PERCENT=32
                data: {
                    'MODEL': '',
                    'dev': '',
                    'CALC_TYPE': 'CALC',
                    'LOAN_AMOUNT': $('#amount_of_credit_input').val().replace(/\s/g, ''),
                    'LOAN_AMOUNT_RANGE': $('#amount_of_credit_input').val().replace(/\s/g, ''),
                    'LOAN_LENGTH': $('#loan_length_input').val(),
                    'LOAN_LENGTH_RANGE': $('#loan_length_input').val(),
                    'LOAN_PERCENT': $('#loan_percent_input').val(),
                    'lang': lang, 
                    'one': $('#one').val(),
                    'notValid': notValid,
                    'percent': parseFloat($('#mortgageInsuranceRate').val()),
                    'collateralTypeId' : parseInt($('#collateralTypeId').val())
                },
                success: function (response) {
                    $('<div>').append(response).appendTo('.jq-calc-result');
                    var date = new Date().toISOString().substr(0, 10);

                    if(parseInt($('#collateralTypeId').val())===1){
                        var notValid =  parseInt($('#notValid').val());
                        var loanSec = 'Залог приобретаемого транспортного средства+полис риска непогашения кредита на сумму ';
                        var propertyValuation1 = 0;
                        var insuranceObject = 0;
                        var insuranceRisk = parseInt(parseInt($('#amount_of_credit_input').val().replace(/\s/g, ''))*1.2 * parseFloat($('#mortgageInsuranceRate').val())/1200*parseInt($('#loan_length_input').val())).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2})
                        var stateDuty = ((parseInt($('#amount_of_credit_input').val().replace(/\s/g, '')) * 1.2) >= 570000000 ? 337500 : 570000);
                        var loanSecValue = (parseInt($('#amount_of_credit_input').val().replace(/\s/g, '')) * 1.2).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2});
                        var additionAmount = insuranceRisk;
                        $('#pdf').remove();
                        $('#pdf-box').append('<a id="pdf" style="font-size: 18px; font-weight: bold; color: #222;" href="https://api.kapitalbank.uz/api/v1/pdf/getInformationSheet?amount='+parseInt($('#amount_of_credit_input').val().replace(/\s/g, '')).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' сум'+'&date='+date.split('-')[2] + '.' + date.split('-')[1] + '.' + date.split('-')[0] +'&mortgage=Потребительский кредит (приобретение нового автотранспортного средства, произведенного в Республике Узбекистан)&money='+ $('#money').text() + ' сум'+'&term='+ $('#loan_length_input').val()+'&rate='+$('#loan_percent_input').val()+'%25&cost='+$('#psk').text()+'25&singleAmount='+$('.js-monthly-payment').text() + ' сум'+'&commission=0.00 сум&propertyValuation1= 0,00 сум'+'&propertyValuation2=0,00 сум'+'&insuranceObject='+ insuranceObject.toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2})+' сум&stateDuty= 0,00 сум'+'&insuranceRisk='+insuranceRisk+' сум&others=0,00 сум&increasedAmount=34.50 процентов годовых&loanSecurity='+loanSec + ' '+ loanSecValue + ' сум'+'&additionAmount='+additionAmount.toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' сум'+'">Скачать информационный лист</a>');
                    }
                    else if(parseInt($('#collateralTypeId').val())===2){
                        var loanSec = 'Депозит денежных средств на сумму ';
                        var notValid =  parseInt($('#notValid').val());
                        var propertyValuation1 = notValid;
                        var loanSecValue = (parseInt($('#amount_of_credit_input').val().replace(/\s/g, '')) * 1.05).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2});

                        if(!notValid){
                            notValid = '0.00';
                            var propertyValuation1 = 0;
                        }

                        var insuranceObject =  0
                        var insuranceRisk = 0;
                        var stateDuty = ((parseInt($('#amount_of_credit_input').val().replace(/\s/g, '')) * 1.25) >= 570000000 ? 337500 : 570000);
                        var additionAmount = 0 ;

                        $('#pdf').remove();
                            $('#pdf-box').append('<a id="pdf" style="font-size: 18px; font-weight: bold; color: #222;" href="https://api.kapitalbank.uz/api/v1/pdf/getInformationSheet?amount='+parseInt($('#amount_of_credit_input').val().replace(/\s/g, '')).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' сум'+'&date='+date.split('-')[2] + '.' + date.split('-')[1] + '.' + date.split('-')[0] +'&mortgage=Потребительский кредит (приобретение нового автотранспортного средства, произведенного в Республике Узбекистан)&money='+ $('#money').text() + ' сум'+'&term='+ $('#loan_length_input').val()+'&rate='+$('#loan_percent_input').val()+'%25&cost='+$('#psk').text()+'25&singleAmount='+$('.js-monthly-payment').text() + ' сум'+'&commission=0.00 сум&propertyValuation1= 0,00 сум'+'&propertyValuation2=0,00 сум'+'&insuranceObject='+ insuranceObject.toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2})+' сум&stateDuty= 0,00 сум'+'&insuranceRisk='+insuranceRisk+' сум&others=0,00 сум&increasedAmount=34.50 процентов годовых&loanSecurity='+loanSec + ' '+ loanSecValue + ' сум'+'&additionAmount='+additionAmount.toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' сум'+'">Скачать информационный лист</a>');
                    }
                }
            });
        })
    }

    var calcCreditAnswer = {};
    calcCreditAnswer.objResult2 = $('.js-calc-result-2');
    calcCreditAnswer.objMonthlyPayment = calcCreditAnswer.objResult2.find('.js-monthly-payment');
    calcCreditAnswer.objOverpayment = calcCreditAnswer.objResult2.find('.js-overpayment');

    <?php
        if($ONE==1){ ?>
        calcCreditAnswer.objMonthlyPayment.html('<?=number_format($iMonthlyPayment,2,'.',' ')?>');
    <?php } ?>

    calcCreditAnswer.objOverpayment.html('<?=number_format($itogo_e,2,'.',' ')?>');
</script>