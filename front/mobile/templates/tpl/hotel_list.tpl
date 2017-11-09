<!--{foreach from=$hotel_list item=hotel}-->
<a href="####" class="fl five" onclick="select_room({$hotel.id})">{$hotel.hotel_name}</a>
<!--{/foreach}-->
<i class="fl">{$utf8_str.Explain}</i>