<div class="clear"></div>
<!--{foreach from=$room_list item=room key=key}-->

<div class="daxiao">
    <label class="checkbox" for="c{$key}">
        <input type="checkbox" data-toggle="checkbox" id="c{$key}" onchange="check_room(this, '{$room.id}', '{$room.room_type}')">
        {$room.type_name}<s>{$utf8_str.Mark}{$room.room_money}/{$utf8_str.Company}</s>
    </label>
    <input type="hidden" name="room_id[]" value="{$room.id}">
</div>
<div class="hidden">
    <div class="clear"></div>
    <div class="ruzhuriqi">
        <div class="fl city2">
            <p>{$utf8_str.start_date}</p>
            <div class="dropdown">
                <input style="border:1px solid #0e407c;border-radius:4px; -webkit-border-radius:4px;padding:8px 4px;" class="date form_date2" name="hotel_start_date[]" id="start_date_{$room.id}_{$room.room_type}" type="text"/>
            </div>
        </div>
        <div class="fl city2" style="margin-left:20px;">
            <p>{$utf8_str.end_date}</p>
            <div class="dropdown">
                <input style="border:1px solid #0e407c;border-radius:4px; -webkit-border-radius:4px;padding:8px 4px;" class="date form_date2" name="hotel_end_date[]" id="end_date_{$room.id}_{$room.room_type}" type="text"/>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div>
        <div class="shuliang-blue fl">
            <p>{$utf8_str.home_t}</p>

            <div class="slform">
                <div class="fl hao"><a href="javascript:room_num_jian('r_{$key}','p_{$key}','{$room.people_num}')">-</a>
                </div>
                <div class="fl"><input type="text" id="r_{$key}" name="room_number[]" value="0" readonly></div>
                <div class="fl hao"><a href="javascript:room_num_jia('r_{$key}','p_{$key}')">+</a></div>
            </div>
        </div>
        <div class="shuliang-blue fl" style="margin-left:20px;">
            <p>{$utf8_str.people_t}</p>

            <div class="slform">
                <div class="fl hao"><a href="javascript:people_num_jian('p_{$key}','r_{$key}')">-</a></div>
                <div class="fl"><input type="text" id="p_{$key}" value="0" name="people_number[]" readonly></div>
                <div class="fl hao"><a
                            href="javascript:people_num_jia('p_{$key}','{$room.people_num}','r_{$key}')">+</a></div>
                (一间房人数最多为{$room.people_num}人)
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<!--{/foreach}-->

<!--控制酒店的间数和人数-->
<!--start-->
<script>
    //间数 r1=房间数的ID r2=人数的ID r3=一间房最多的人数
    function room_num_jia(r1, r2) {
        var num = new Number(document.getElementById(r1).value);
        var num2 = new Number(document.getElementById(r2).value);
        if (num2 <= num) {
            document.getElementById(r1).value = num + 1;
            document.getElementById(r2).value = num + 1;
            return false;
        } else {
            document.getElementById(r1).value = num + 1;
        }
    }

    function room_num_jian(r1, r2, r3) {
        var num = new Number(document.getElementById(r1).value);
        var num2 = new Number(document.getElementById(r2).value);
        if (num <= 1) {
            return false;
        }
        else if (num2 < num) {
            return false;
        }
        else if (num2 <= (num - 1) * r3) {
            document.getElementById(r2).value = num2;
            document.getElementById(r1).value = num - 1;
        }
        else if (num2 >= (num - 1) * r3) {
            document.getElementById(r2).value = (num - 1) * r3;
            document.getElementById(r1).value = num - 1;
        }
        else {
            document.getElementById(r1).value = num - 1;

        }
    }

    //人数  r1=人数的ID r2=一间房最多的人数 r3=房间数的ID
    function people_num_jia(r1, r2, r3) {
        var num = new Number(document.getElementById(r1).value);
        var num2 = new Number(document.getElementById(r3).value);
        if (num >= r2 * num2) {
            return false;
        }
        else {
            document.getElementById(r1).value = num + 1;
        }
    }

    function people_num_jian(r1, r3) {
        var num = new Number(document.getElementById(r1).value);
        var num2 = new Number(document.getElementById(r3).value);
        if (num <= num2) {
            return false;
        }
        else {
            document.getElementById(r1).value = num - 1;
        }
    }

</script>
<!--end-->
<script>
    $('[data-toggle="checkbox"]').radiocheck();
</script>