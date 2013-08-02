<table cellpadding="2" cellspacing="1" width="98%">
    <tr> 
        <td>地图接口类型</td>
        <td>
            <input type="radio" onChange="mapclass_msg();" onclick="Marker('51map')" name="setting[mapclass]" value="1" <?= $setting['mapclass'] == 1 ? 'checked' : '' ?> > 51地图 
            <input type="radio" onChange="mapclass_msg();" onclick="Marker('mapbar')" name="setting[mapclass]" value="2" <?= $setting['mapclass'] == 2 ? 'checked' : '' ?> > 图吧地图 
            <input type="radio" onChange="mapclass_msg();" onclick="Marker('google')" name="setting[mapclass]" value="0" <?= $setting['mapclass'] ? '' : 'checked' ?> > google</td>
    </tr>
    <tr> 
        <td>默认X轴坐标</td>
        <td><input type="text" name="setting[mapx]" value="<?= $setting['mapx'] ?>" id="mapx" size="20"></td>
    </tr>
    <tr> 
        <td>默认Y轴坐标</td>
        <td><input type="text" name="setting[mapy]" value="<?= $setting['mapy'] ?>" id="mapy" size="20"></td>
    </tr>
    <tr> 
        <td>默认缩放比例: GOOGLE以及图吧 此项设置为接口KEY<br />
            【<a href="http://code.google.com/intl/zh-CN/apis/maps/signup.html" target="_blank">GOOGLE KEY 申请</a>】
            【<a href="http://union.mapbar.com/apidoc/key.html" target="_blank" >图吧 KEY申请 </a>】
        </td>
        <td><input type="text" name="setting[mapz]" id="mapz" value="<?= $setting['mapz'] ?>" size="20"> </td>
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
    function mapclass_msg(){
        var old_class=<?= $setting['mapclass'] ?>;
        var old_class= old_class ? old_class ==1 ?"51地图" : "图吧地图": "google地图";
        alert("您好，由于不同的地图供应商，数据并不通用，改变地图接口类型将会导致以前存在的数据无法使用，请慎重选择！\n\n您先前默认的地图供应商为  " + old_class);
    }
</script>