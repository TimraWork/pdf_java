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
                "         \n" +
                "        }\n" +
                "        .italic{\n" +
                "            font-style: italic;\n" +
                "        }\n" +
                "        .title{\n" +
                "            font-weight: 700;\n" +
                "            font-size: 12pt;\n" +
                "            text-align: center;\n" +
                "        }\n" +
                "        .text-center{\n" +
                "            text-align: center;\n" +
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
                "                <td>Официальный сайт</td>\n" +
                "                <td>Kapitalbank.uz</td>\n" +
                "            </tr>\n" +                
                "            <tr>\n" +
                "                <td>Единая информационная служба</td>\n" +
                "                <td>(+998 71) 200-15-15</td>\n" +
                "            </tr>\n" +
                "        </table>\n" +
                "        <p class=\"bold\">Раздел 1. Сведения по кредиту</p>\n" +
                "        <table cellspacing=\"0\" border=\"1\" cellpadding=\"5\" width=\"100%\">\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">1. Тип кредита</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ mortgage +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">2. Цель кредита (вид)</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ mortgage +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-cly1\">3. Размер кредита(сум)</td>\n" +
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
                "                <td class=\"tg-0lax\">6.Общая сумма, подлежащая выплате </td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ cost +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">7.Период отсрочки погашения кредита (при наличии)</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">1 в месяц</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">8. Периодичность платежей</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">ежемесячно</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">9. Способ погашения кредита</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\" rowspan=\"2\">аннуитет</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\"> (дифференцированный, аннуитет и пр.) </td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">10. Сумма разового платежа в период платежей (в месяц)</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ singleAmount +" сум</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">11. Форма предоставления кредита </td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">В соотвествии с действующим законодательством Руз, Кредитной политикой банка.  сум</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">12. Дополнительные расходы, связанные с кредитом, в том числе:</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ additionAmount +". 00 сум</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">12.1 банковская комиссия и сборы по видам</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\"> 0.00 </td>\n" +
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
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">6) Услуги кадастровой службы</td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\">"+ others +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">13. Стоимость кредита </td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\"  rowspan=\"2\" >"+ cost +"</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-cly1 italic\">(включая проценты и расходы по обслуживанию кредита)</td>\n" +
                "              </tr>\n" +
                "              <tr>\n" +
                "                <td class=\"tg-0lax\">14.Срок рассмотрения кредитной заявки </td>\n" +
                "                <td class=\"tg-0lax\" colspan=\"2\"  rowspan=\"2\" >В течение 5 рабочих дней</td>\n" +
                "              </tr>\n" +
                "        </table>\n" +
                "        <p class=\"italic\">*) Настоящий лист не заменяет кредитный договор или заявку на получение кредита, а помогает сравнить условия кредитования различных банков и осуществить нужный выбор.</p>\n" +
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
                "        <p class=\"title\">Внимательно изучите, прежде чем соглашаться на получение кредита!</p>\n" +
                "        <p>Вы вправе получить из банка полную и подробную информацию об условиях и стоимости кредита/микрозайма, порядке платежей и расчетов (проценты, штрафы и пени), Ваших правах и обязанностях по кредитному договору (договору микрозайма), о рисках и ответственности, которые могут вытекать из кредитного договора (договора микрозайма), а также по другим вопросам, которые Вам неясны.</p>\n" +
                "        <p>При наличии жалоб Вы можете обратиться по телефону (+998 71) 200-45-45 или направить свое обращение по почтовому адресу: г. Ташкент, Юнусабадский район, ул. Сайилгох, д.7а, либо на электронный адрес банка info@kapitalbank.uz.</p>\n" +
                "        <p> ТОЧНОСТЬ ИНФОРМАЦИОННОГО ЛИСТ ИСТИНА ПОДТВЕРЖДАЕТСЯ. </p>\n" +
                "        <table cellspacing=\"0\" border=\"none\" cellpadding=\"5\" width=\"100%\">\n" +
                "            <tr>\n" +
                "                <td class=\"tg-0lax text-center\">________________________________</td>\n" +
                "                <td class=\"tg-0lax text-center\">________________________________</td>\n" +
                "            </tr>\n" +
                "            <tr>\n" +
                "                <td class=\"tg-0lax text-center italic\">(специалист банка Ф.И.О и должность)</td>\n" +
                "                <td class=\"tg-0lax text-center italic\">Дата заполнения</td>\n" +
                "            </tr>\n" +
                "        </table>\n" +
                "        <p class=\"italic\">* Эта форма не заменяет кредитный договор или кредитную заявку, но помогает сравнить условия кредитования разных банков и сделать правильный выбор.</p>\n" +
                "    </div>\n" +
                "</body>\n" +
                "</html>";
    }
}
