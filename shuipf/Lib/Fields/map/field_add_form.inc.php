<font color="red" size="4">&nbsp;&nbsp;请慎重选择接口类型，由于每个接入商数据不是通用的后期将无法更改！！，添加地图字段以后还需要修改模板，请查看shuipf\Lib\Action\Models\fields\map\目录下的“地图调用说明.txt”</font>
<table cellpadding="2" cellspacing="1" width="98%">
    <tr> 
        <td width="300">地图接口类型</td>
        <td>
            <input type="radio" onclick="Marker('51map')" name="setting[mapclass]" value="1" checked > 51地图 
            <input type="radio" onclick="Marker('mapbar')" name="setting[mapclass]" value="2"> 图吧地图 
            <input type="radio" onclick="Marker('google')" name="setting[mapclass]" value="0" > google</td>
    </tr>
    <tr> 
        <td>默认X轴坐标</td>
        <td><input type="text" id="mapx" name="setting[mapx]" value="12010241" size="20"></td>
    </tr>
    <tr> 
        <td>默认Y轴坐标</td>
        <td><input type="text" id="mapy" name="setting[mapy]" value="3086626" size="20"></td>
    </tr>
    <tr> 
        <td>默认缩放比例: GOOGLE以及图吧 此项设置为接口KEY<br />
            【<a href="http://code.google.com/intl/zh-CN/apis/maps/signup.html" target="_blank">GOOGLE KEY 申请</a>】
            【<a href="http://union.mapbar.com/apidoc/key.html" target="_blank" >图吧 KEY申请 </a>】
        </td>
        <td><input type="text" id="mapz" name="setting[mapz]" value="6" size="20"> </td>
    </tr>
</table>
<script type="text/javascript">
    function Marker(id){
        if(id=="google"){
            $("#mapx").val("30.864316827640206");
            $("#mapy").val("120.09546875953674");
            $("#mapz").val("ABQIAAAAo8SHyxPUU0PK0eLil2cLRBROdDdf41y3Vbz6JKPNNoyAV3nPbBTPs9lXLdVMXY8AJmP_SLRdWNzmoA");
        }else if(id=="mapbar"){
            $("#mapx").val("30.86522");
            $("#mapy").val("120.10205");
            $("#mapz").val("aCW9cItqL7m3bhAqLhNpEeTyOIJzEeFhMYD5NYZfNBDzNRJh@YOFLYWqEqAApAzIqFYhZZYNIZY@LqqIqEYyALILhLLMLAALhA@A5LqZJJLqqfYALmEqYqYJhYI3FCt=");
        }else{
            $("#mapx").val("12010241");
            $("#mapy").val("3086626");
            $("#mapz").val("6");
        }
    }
</script>