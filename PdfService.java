package application.services;

import com.itextpdf.text.Document;
import com.itextpdf.text.pdf.PdfWriter;
import com.itextpdf.tool.xml.XMLWorkerHelper;
import org.jsoup.Jsoup;
import org.springframework.core.io.InputStreamResource;
import org.springframework.core.io.Resource;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Service;

import java.io.*;
import java.nio.charset.StandardCharsets;

@Service
public class PdfService {

    public ResponseEntity<Resource> getInformationSheetPdf(
            String date,
            String amount,
            String term,
            String rate,
            String cost,
            String singleAmount,
            String additionAmount,
            String commission,
            String propertyValuation1,
            String propertyValuation2,
            String insuranceObject,
            String insuranceRisk,
            String stateDuty,
            String others,
            String increasedAmount,
            String loanSecurity,
            String mortgage,
            String money

    ) {
        try {
            org.jsoup.nodes.Document doc = Jsoup.parse(getInformationSheetHtml(date, amount, term, rate, cost, singleAmount, additionAmount, commission, propertyValuation1, propertyValuation2, insuranceObject, insuranceRisk, stateDuty, others, increasedAmount, loanSecurity, mortgage, money));

            Document document = new Document();
            long now = System.currentTimeMillis();
            PdfWriter writer = PdfWriter.getInstance(document, new FileOutputStream("Informacionnyj_list_" + now +  ".pdf"));

            document.open();
            XMLWorkerHelper worker = XMLWorkerHelper.getInstance();
            InputStream is = new ByteArrayInputStream(doc.toString().getBytes(StandardCharsets.UTF_8));
            worker.parseXHtml(writer, document, is, StandardCharsets.UTF_8);

            document.close();

            File file = new File("Informacionnyj_list_" + now +  ".pdf");
            InputStreamResource resource = new InputStreamResource(new FileInputStream(file));
            HttpHeaders header = new HttpHeaders();
            header.add(HttpHeaders.CONTENT_DISPOSITION, String.format("attachment; filename=%s", file.getName()));
            header.add("Cache-Control", "no-cache, no-store, must-revalidate");
            header.add("Pragma", "no-cache");
            header.add("Expires", "0");
            file.delete();
            return ResponseEntity.ok()
                    .headers(header)
                    .contentLength(file.length())
                    .contentType(MediaType.parseMediaType("application/octet-stream"))
                    .body(resource);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return new ResponseEntity<>(HttpStatus.BAD_REQUEST);
    }

    private String getInformationSheetHtml(
            String date,
            String amount,
            String term,
            String rate,
            String cost,
            String singleAmount,
            String additionAmount,
            String commission,
            String propertyValuation1,
            String propertyValuation2,
            String insuranceObject,
            String insuranceRisk,
            String stateDuty,
            String others,
            String increasedAmount,
            String loanSecurity,
            String mortgage,
            String money
    ) {
        return "<!DOCTYPE html>\n" +
                "<html lang=\"en\">\n" +
                "<head>\n" +
                "    <style>\n" +
                "        h2{\n" +
                "            text-align: center;\n" +
                "        }\n" +
                "        p{\n" +
                "            text-align: center;\n" +
                "        }\n" +
                "        .main{\n" +
                "            margin: 70px 225px;\n" +
                "            font-family: Arial,serif;\n" +
                "    \t\tfont-size: 10pt;\n" +
                "    \t\tfont-style: normal;\n" +
                "    \t\tfont-variant: normal;\n" +
                "    \t\tfont-weight: normal;\n" +
                "    \t\tline-height: normal;\n" +
                "        }\n" +
                "        .bold{\n" +
                "            font-weight: 700;\n" +
                "\n" +
                "        }\n" +
                "    </style>\n" +
                "</head>\n" +
                "<body>\n" +
                "    <div class=\"main\">\n" +
                "        <h2>ИНФОРМАЦИОННЫЙ ЛИСТ</h2>\n" +
                "        <p>о основных условиях кредита</p>\n" +
                "        <table cellspacing=\"0\" border=\"1\" cellpadding=\"5\" width=\"100%\">\n" +
                "            <tr>\n" +
                "                <td>Наименование банка</td>\n" +
                "                <td>АКБ \"Капиталбанк\"</td>\n" +
                "            </tr>\n" +
                "            <tr>\n" +
                "                <td class=\"tg-0lax\">Кем заполнен настоящий лист</td>\n" +
                "                <td class=\"tg-0lax\" rowspan=\"2\">Заполнено на сайте банка</td>\n" +
                "            </tr>\n" +
                "            <tr>\n" +
                "                <td class=\"tg-0lax\">(Ф.И.О. и должность)</td>\n" +
                "            </tr>\n" +
                "            <tr>\n" +
                "                <td>Дата заполнения</td>\n" +
                "                <td>"+ date +"</td>\n" +
                "            </tr>\n" +
                "        </table>\n" +
                "        <p class=\"bold\">Раздел 1. Сведения по кредиту</p>\n" +
                "        <table cellspacing=\"0\" border=\"1\" cellpadding=\"5\" width=\"100%\">\n" +
                "            <tr>\n" +
                "                <td class=\"tg-0lax\">1. Цель кредита (вид)</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ mortgage +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">2. Валюта кредита</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">Сум</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-cly1\">3. Размер кредита</td>\n" +
                "                <td class=\"tg-cly1\" colspan=\"2\">"+ amount +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">4. Срок кредита (месяц)</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ term +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\" rowspan=\"2\">5. Процентная ставка кредита (в номинальном размере)</td>\n" +
                "                <td class=\"tg-0lax\">"+ rate +"</td>\n" +
                "                <td class=\"tg-0lax\">"+ money +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-cly1\">(в виде процентов)</td>\n" +
                "                <td class=\"tg-cly1\">(в денежном выражении \n" +
                "                    на полный срок кредита )</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">6. Стоимость кредита </td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\" rowspan=\"2\">"+ cost +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">(включая проценты и расходы по обслуживанию кредита)</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">7. Периодичность платежей</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">ежемесячно</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">8. Способ погашения кредита</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\" rowspan=\"2\">аннуитет</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\"> (дифференцированный, аннуитет и пр.) </td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">9. Сумма разового платежа в период платежей (в месяц)</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ singleAmount +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">10. Дополнительные расходы, связанные с кредитом, в том числе:</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ additionAmount +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">10.1 банковская комиссия и сборы по видам</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ commission +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">1) Оценка имущества</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ propertyValuation1 +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">10.2 услуги третьих лиц:</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\"></td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">1) Оценка имущества</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ propertyValuation2 +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">2) Страхование объекта залога, на период страхования</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ insuranceObject +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">3) Страхование риска непогашения кредита, на период страхования</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ insuranceRisk +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">4) Гос.пошлина за удостоверение залога</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ stateDuty +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">5) Другие платные услуги нотариуса (включает проверку запрета, консультацию и подготовку проекта договора)</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ others +"</td>\n" +
                "              </tr>\n" +
                "        </table>\n" +
                "        <p class=\"bold\">Перед согласием на получение кредита внимательно ознакомьтесь!</p>\n" +
                "        <p>*) Настоящий лист не заменяет кредитный договор или заявку на получение кредита, а помогает сравнить условия кредитования различных банков и осуществить нужный выбор.</p>\n" +
                "        <p class=\"bold\">Раздел 2. Другие значимые условия</p>\n" +
                "        <table cellspacing=\"0\" border=\"1\" cellpadding=\"5\" width=\"100%\">\n" +
                "            <tr>\n" +
                "                <td>1. Неустойка за нарушение условий кредита</td>\n" +
                "                <td>За несвоевременное оформление и/или не предоставление Заемщиком договора залога, договора страхования и полиса страхования Предмета залога и/или договора поручительства, в сроки и на условиях, предусмотренных в п.4.1. настоящего договора, Банк имеет право взыскать пеню в размере 0,1% за каждый день просрочки от суммы не оформленного в срок обеспечения, но не более 10% от суммы не оформленного в срок обеспечения.</td>                \n" +
                "            </tr>\n" +
                "            <tr>\n" +
                "                <td>2. Размер повышенной процентной ставки за просроченные платежи</td>\n" +
                "                <td>"+ increasedAmount +"</td>\n" +
                "            </tr>\n" +
                "            <tr>\n" +
                "                <td>3. Обеспечение кредита (минимальные требования к предмету обеспечения, минимальная стоимость залога)</td>\n" +
                "                <td>"+ loanSecurity +"</td>\n" +
                "            </tr>\n" +
                "        </table>\n" +
                "        <p class=\"bold\">Сведения, указанные в разделах 1 и 2 настоящего информационного листа, не считаются окончательными и могут быть изменены в кредитном договоре.</p>\n" +
                "        <p class=\"bold\">Раздел 3. Перечень документов, которые необходимо\n" +
                "            представить для получения кредита</p>\n" +
                "        <p style=\"text-align: left;\">1. Паспорт (оригинал, копия от которыой снимается сотрудником банка)</p>\n" +
                "        <p style=\"text-align: left;\">2. Справка о доходах заемщика</p>\n" +
                "        <p style=\"text-align: left;\">3. При целевых кредитах по приобретению имущества/услуг/работ, соотвествующий договор по приобретению имущества/услу/работ.</p>\n" +
                "        <p style=\"text-align: left;\">4. При недостаточности доходов, паспорт и справка о доходах созаемщика</p>\n" +
                "        <p class=\"bold\">Раздел 4. Ваши права в качестве будущего получателя кредита</p>\n" +
                "        <p style=\"text-align: left;\">1. Вы свободны в выборе банка и банковских услуг.</p>\n" +
                "        <p style=\"text-align: left;\">2. Вы вправе отказаться от подписания кредитного договора или других договоров и соглашений.</p>\n" +
                "        <p style=\"text-align: left;\">3. При заключении договора Вы имеете право выбирать язык составления договора (государственный или русский язык).</p>\n" +
                "        <p style=\"text-align: left;\">4. Вы вправе взять проект кредитного договора для ознакомления за пределами банка и получения консультаций.</p>\n" +
                "        <p style=\"text-align: left;\">5. Вы вправе получить из банка полную и подробную информацию об условиях и стоимости кредита, порядке платежей и расчетов (проценты, штрафы и пени), Ваших правах и обязанностях по кредитному договору, о рисках и ответственности, которые могут вытекать из кредитного договора, а также по другим вопросам, которые Вам неясны.</p>\n" +
                "        <p style=\"text-align: left;\">6. После подписания кредитного договора, но до фактического получения денежных средств, Вы имеете право отказаться от получения кредита без каких-либо платежей.</p>\n" +
                "        <p style=\"text-align: left;\">Сотрудники банка не имеют право оказывать содействие получению кредита за плату.</p>\n" +
                "        <p style=\"text-align: left;\">При наличии жалоб Вы можете обратиться по телефону 99871 200 15 15 или направить свое обращение по почтовому адресу: г. Ташкент, Юнусабадский район, ул. Сайилгох, д.7а, либо на электронный адрес банка ozod.ochilov@kapitalbank.uz.</p>\n" +
                "        <p style=\"text-align: left;\"></p>\n" +
                "        <p style=\"text-align: left; font-style: italic\">* Дополнительная информация:</p>\n" +
                "        <p style=\"text-align: left;\">1.   Банк обязан проверить Ваше финансовое состояние, с учетом нижеследующих параметров:</p>\n" +
                "        <p style=\"text-align: left; font-style: italic\">  * соотвествие ваших доходов Вашей долговой нагрузке;</p>\n" +
                "        <p style=\"text-align: left; font-style: italic\">  * ожидаемые даты поступления Ваших доходов в период пользования кредитом;</p>\n" +
                "        <p style=\"text-align: left; font-style: italic\">  * вероятность возникновения непреодолимых обстоятельств, приводящим к невыполнению Ваших обязательств по получаемому кредиту;</p>\n" +
                "        <p style=\"text-align: left; font-style: italic\">  * при невыполнении условий кредитного договора, предусмотрены штрафные санкции и проценты по повышенным ставкам.</p>\n" +
                "    </div>\n" +
                "</body>\n" +
                "</html>";
    }
}
