<style>
    .pdf{
	 margin:0 auto;
        width:100%;
      }
    h1{
        margin: 0 auto;
        text-align:center;
        font-size: 14pt;
        font-weight:normal;
    }
    h2{
        margin: 0 auto;
        text-align:center;
        font-size:12pt;
        font-weight:normal;
    }
    h3{
        margin: 0 auto;
        text-align:center;
        font-size:12pt;
        font-weight:normal;
    }
    h4{

        text-align:center;
        font-size:12pt;
        font-weight:normal;
    }
    h5{
        margin: 0 auto;
        text-align:center;
        font-size:11pt;
        font-weight:normal;
    }
    img{
        display:block;
        border:none;
        margin:0 auto;
    }
    p{
        margin:30px auto;
        text-align:center;
        font-size:10pt;
    }
    .tupian{

        overflow:hidden;
    /*    margin-bottom:20px;*/
    }
    .fudong{
        float:left;
        width:120px;
    }
    .erweima{
       width:30%;
        margin:0 auto;
    }
    table{
        width:100%;
    }
    td{
        width:33%;
    }
</style>
<div class="pdf">
    <div class="tupian">
        <img  class="fudong" src="images/logo_pdf.png" />
    </div>
    <h1>OFFICIAL TICKET CONFIRMATION</h1>
    <h2>官方购票凭证</h2>
    <h1>GUEST CONFIRMATION CERTIFICATE NUMBER</h1>
    <h2>凭证号码</h2>
    <h1>ORDER NUMBER:{$order_sn}</h1>
    <h2>订单号</h2>
    <table>
        <tr>
            <td></td>
            <td><img src="{$qr_url}"/> </td>
            <td></td>
        </tr>

    </table>
    <p>This is to certify that official Guest Confirmation Certificate
        Number:{$order_sn} has been identified as a ticket purchase to attend sports event.
    </p>
    <p>
        If a visa is required in advance for you to travel,you may produce this certificate
        (as well as other required documents) when applying for your visa.
        THIS IS NOT A TICKET
    </p>
    <h3>THIS IS NOT A TICKET</h3>
    <h4>此购票凭证证明持票人已成功购票并准予参加赛事<br />
        此凭证可提供给使馆申请签证的辅助资料</h4>
    <h5>请注意此凭证不是球票</h5>
</div>