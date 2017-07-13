	$(document).ready(function ()
{
$('#name').bind("mouseover keyup",function()
{
var data = $('#name').serialize();
$.post(
'conf/validate.php',
data,
function(data)
{
$('#namer').html(data).show();
},
'html'
);
});
});
$(document).ready(function ()
{
$('#lname').bind("mouseover keyup",function()
{
var data = $('#lname').serialize();
$.post(
'conf/validate.php',
data,
function(data)
{
$('#lnamer').html(data).show();
},
'html'
);
});
});