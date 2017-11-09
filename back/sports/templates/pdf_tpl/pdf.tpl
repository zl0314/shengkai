<style>
    table {
        width: 100%;
        border: 1px solid #000;
        collapse: 1px;
    }
    td, tr {
        border: 1px solid #000;
        line-height: 10px;
    }
    .bg_c{
        background-color: #eeeeee;
    }
   .biaoti{
       background-color:#000000;
        color:#ffffff;
    }
    .right{
        text-align: right;;
    }
    .left{
        text-align: left;;
    }
</style>
<table>
    <tr>
        <td class="biaoti" colspan="4" style="text-align: center;">持票人信息</td>
    </tr>
    <tr class="bg_c" >
        <td >客户姓名：</td>
        <td >{$info.cn_customer_name}</td>
        <td>Customer's Name：</td>
        <td >{$info.us_customer_name}</td>
    </tr>
    <tr>
        <td>特别称谓：</td>
        <td >{if $info.gender_appellation == 1}先生{else}女士{/if}</td>
        <td>Title：</td>
        <td>{if $info.gender_appellation == 1}Mr.{else}Mrs.{/if}</td>
    </tr>
    <tr class="bg_c">
        <td>护照号码：</td>
        <td >{$info.passport_number}</td>
        <td>Passports Number：</td>
        <td >{$info.passport_number}</td>
    </tr>
    <tr>
        <td>出生日期：</td>
        <td >{$info.date_birth}</td>
        <td>Date of Birth：</td>
        <td >{$info.date_birth}</td>
    </tr>
    <tr class="bg_c">
        <td>护照签发日期：</td>
        <td >{$info.issue_date}</td>
        <td>Issue date：</td>
        <td >{$info.issue_date}</td>
    </tr>
    <tr>
        <td>护照有效期：</td>
        <td >{$info.expire_date}</td>
        <td>Expire date：</td>
        <td >{$info.expire_date}</td>
    </tr>
    <tr class="bg_c">
        <td>国籍：</td>
        <td >{if $info.cn_nationality == 0}中国{else}美国{/if}</td>
        <td>Nationality：</td>
        <td >{if $info.cn_nationality == 0}China{else}USA{/if}</td>
    </tr>
    <tr>
        <td>街道门牌号：</td>
        <td >{$info.cn_name_street}</td>
        <td>Name of the Street and No：</td>
        <td >{$info.us_name_street}</td>
    </tr>
    <tr class="bg_c">
        <td>邮编：</td>
        <td>{$info.post_code}</td>
        <td>Post Code：</td>
        <td>{$info.post_code}</td>
    </tr>
    <tr>
        <td>国家：</td>
        <td>{$info.cn_country|address} {$info.cn_state|address}</td>
        <td>Country：</td>
        <td>{$info.cn_country|address} {$info.cn_state|address}</td>
    </tr>
    <tr class="bg_c">
        <td>座机电话：</td>
        <td>{$info.telephone}</td>
        <td>Telephone No：</td>
        <td>{$info.telephone}</td>
    </tr>
    <tr>
        <td>手机电话：</td>
        <td>{$info.mobile}</td>
        <td>Mobile Telephone No：</td>
        <td>{$info.mobile}</td>
    </tr>
    <tr class="bg_c">
        <td>电子邮件：</td>
        <td>{$info.mail}</td>
        <td>E-mail：</td>
        <td>{$info.mail}</td>
    </tr>
    <tr>
        <td>备注：</td>
        <td>{$info.cn_remarks}</td>
        <td >Type of Product：</td>
        <td>{$info.cn_remarks}</td>
    </tr>
    <tr>
        <td>票号：</td>
        <td>{$info.ticket_code}</td>
        <td>Ticket No：</td>
        <td>{$info.ticket_code}</td>
    </tr>
</table>