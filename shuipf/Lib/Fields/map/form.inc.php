<?php

function map($field, $value, $fieldinfo) {
    $setting = unserialize($fieldinfo['setting']);
    extract($setting);
    if ($value) {
        $value = explode("|", $value);
        $mapx = $value[0];
        $mapy = $value[1];
        if ($mapclass == 1)
            $mapz = $value[2];
    }
    $data = "<input type='text' size='20' name='info[" . $field . "][mapx]' id='" . $field . "mapx' value='" . $mapx . "' class='input' />&nbsp;&nbsp;&nbsp;<input type='text' size='20' name='info[" . $field . "][mapy]' id='" . $field . "mapy' value='" . $mapy . "' class='input' />";
    if ($mapclass == 1) {
        $mapjs = <<< herf
		<script language="javascript" src="http://api.51ditu.com/js/maps.js"></script>
        <script language="javascript" src="http://api.51ditu.com/js/ezmarker.js"></script>
		<script language="JavaScript">
		<!--
        //setMap是ezmarker内部定义的接口，这里可以根据实际需要实现该接口
        function setMap(point,zoom){
			document.getElementById("{$field}mapx").value=point.getLongitude();
			document.getElementById("{$field}mapy").value=point.getLatitude();
            document.getElementById("{$field}mapz").value=zoom;
        }
        var ezmarker = new LTEZMarker("{$field}");	
		//var c = "huzhou";
		//ezmarker.setDefaultView(c,3);
		ezmarker.setValue(new LTPoint({$mapx},{$mapy}),{$mapz});
        LTEvent.addListener(ezmarker,"mark",setMap);//"mark"是标注事件
        //-->
        </script>
herf;

        $data .="&nbsp;&nbsp;&nbsp;<input type='text' size='20' name='info[" . $field . "][mapz]' id='" . $field . "mapz' value='" . $mapz . "'/>" . $mapjs;
    } else if ($mapclass == 2) {
        $mapjs = <<< herf
			<div id="{$field}mapdiv" style="position:absolute; top:200px; left:200px; width: 500px; height: 500px; background-color:#F2EFE9; text-align:center;display:none;"><div id='{$field}mapmark_mymap' style='width: 500px; height: 500px;' ></div>
            <br/>
            <input type="button" value="添加标注" onclick="setp_{$field}()"  class='btn' /> <input type="button" id="findp" value="跳到标记处"  onclick="find_{$field}()"  class='btn' /> <input type="button" value="关闭窗口" onclick="$('#{$field}mapdiv').hide();"  class='btn'  />
            </div>
			<script type = "text/javascript" src ="http://union.mapbar.com/apis/maps/free?f=mapi&v=31.2&k={$mapz}"></script> 
<script type="text/javascript"> 
var maplet=null;//地图对象 
var marker_{$field}=null;//标记对象 
var le=null;//缩放级别 
var myEventListener_{$field}=null;//地图click事件句柄 
function initMap_{$field}()//初始化函数 
{  
le=10; //默认缩放级别 
maplet = new Maplet("{$field}mapmark_mymap"); 
//这里可以初始化地图坐标比如从数据库中读取 然后在页面上使用小脚本的形式 
//如: maplet.centerAndZoom(new MPoint(<%=经度%>,<%=维度%> ),<%=缩放级别%>); 
maplet.centerAndZoom(new MPoint({$mapy}, {$mapx}), le);//初始化地图中心点坐标并设定缩放级别 
maplet.addControl(new MStandardControl()); 
} 
function setp_{$field}() 
{ 
if(marker_{$field}){
	maplet.removeOverlay(marker_{$field});
}
maplet.setMode("{$field}bookmark");//设定为添加标记模式 
maplet.setCursorIcon("/statics/images/tack.gif"); //添加鼠标跟随标签 
myEventListener_{$field} = MEvent.bind(maplet, "click", this, addp_{$field}); //注册click事件句柄 
} 
//这里的参数要写全即使你不使用event 
function addp_{$field}(event,point){
//removeMarker_{$field}();
marker_{$field} = new MMarker( point, new MIcon("/statics/images/mapbar_ok_tack.gif", 78, 78)); 
marker_{$field}.bEditable=true; 
marker_{$field}.dragAnimation=true; 
maplet.addOverlay(marker_{$field});//添加标注 
marker_{$field}.setEditable(true); //设定标注编辑状态 
maplet.setMode("pan"); //设定地图为拖动(正常)状态 
le= maplet.getZoomLevel(); //获取当前缩放级别 
 
document.getElementById("{$field}mapx").value=marker_{$field}.pt.lat; 
document.getElementById("{$field}mapy").value=marker_{$field}.pt.lon;
//document.getElementById("{$field}mapz").value=le;
MEvent.removeListener(myEventListener_{$field});//注销事件

MEvent.addListener(maplet, "edit", dragEnd); 
} 
//查找标记 
function find_{$field}(){ 
maplet.centerAndZoom(marker_{$field}.pt, le);//定位标记 
}
function dragEnd_{$field}(overlay){   
	setTimeout(function(){ 
		document.getElementById("{$field}mapx").value=overlay.pt.lat;
		document.getElementById("{$field}mapy").value=overlay.pt.lon; 
		//document.getElementById("{$field}mapz").value=maplet.getZoomLevel();    
        //overlay.setEditable(false);   
        },500);   
}
function removeMarker_{$field}(){   
	var selector = document.getElementById("{$field}mapmark_mymap");   
	var item = selector.options[selector.selectedIndex].value;   
	maplet.removeOverlay(markerArr[item]);   
	selector.removeChild(selector.options[selector.selectedIndex]);      
}   


initMap_{$field}();
</script>
<a onclick="$('#{$field}mapdiv').show();" /><img src="/statics/images/button-f.gif"></a>
herf;
        $data .=$mapjs;
    } else {
        $mapjs = <<< herf
			
			<div id="{$field}mapdiv" style="position:absolute; right:100px;width: 505px; height: 400px; background-color:#F2EFE9; text-align:center;display:none;"><div id='{$field}mark_mymap' style='width: 505px; height: 350px;' ></div>
            <br/>
            <input type="button" value="关闭地图窗口" onClick="$('#{$field}mapdiv').hide();" />
            </div>

			<script src="http://maps.google.com/maps?file=api&v=2&key={$mapz}" type="text/javascript" charset="utf-8"></script>
		   <script type="text/javascript" language="javascript">
		   function atmark_{$field}() { //标注接口开始
				var map = null;
				if (GBrowserIsCompatible()) { //判断是否生成
					var map = new GMap2(document.getElementById('{$field}mark_mymap'));
					map.setCenter(new GLatLng({$mapx},{$mapy}), 14);
					map.addControl(new GSmallMapControl()); //是否显示缩放
					map.addControl(new GMapTypeControl()); //是否显示卫星地图
				}
				map.clearOverlays(marker);   //清除地图上的标记点，否则会显示多个                        
				var Center = map.getCenter();
				var lat = new String(Center.lat());
				var lng = new String(Center.lng());
				setLatLng_{$field}(lat, lng);
				var marker = new GMarker(new GLatLng(lat, lng), {draggable: true});
				GEvent.addListener(marker, "dragend", function() {
				var latlng = marker.getLatLng();
				lat = String(latlng.lat());
				lng = String(latlng.lng());
				setLatLng_{$field}(lat, lng);
			});
			map.addOverlay(marker); // 写入标记到地图上
			}
			function setLatLng_{$field}(lat,lng) {
				document.getElementById("{$field}mapx").value=lat;
				document.getElementById("{$field}mapy").value=lng; 
			}
			</script>
			<a onclick="$('#{$field}mapdiv').show();atmark_{$field}();" /><img src="/statics/images/button-f.gif"></a>
		               
herf;
        $data .=$mapjs;
    }
    return $data;
}

?>