/*
@功能：购物车页面js
@作者：diamondwang
@时间：2013年11月14日
*/

$(function(){
	
	//减少
	$(".reduce_num").click(function(){
		var amount = $(this).parent().find(".amount");
		if (parseInt($(amount).val()) <= 1){
			alert("商品数量最少为1");
		} else{
			$(amount).val(parseInt($(amount).val()) - 1);
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
		var goods_id=$(this).closest('tr').attr('data-id');
		console.debug(goods_id);
		console.debug(amount.val());
		changeAmount(goods_id,$(amount).val());
	});
    $('.changeamount').on('click',function () {
        var goods_id=$(this).closest('tr').attr('data-id');
        self=$(this);
        $.post('changeamount.html',{goods_id:goods_id,amount:0},function () {
            self.closest('tr').remove()
            var total = 0;
            $(".col5 span").each(function(){
                total += parseFloat($(this).text());
            });
            $("#total").text(total.toFixed(2));
        })

    })
	//增加
	$(".add_num").click(function(){
		var amount = $(this).parent().find(".amount");
		$(amount).val(parseInt($(amount).val()) + 1);
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
        var goods_id=$(this).closest('tr').attr('data-id');
        changeAmount(goods_id,$(amount).val());
	});

	//直接输入
	$(".amount").blur(function(){
		if (parseInt($(this).val()) < 1){
			alert("商品数量最少为1");
			$(this).val(1);
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
		var goods_id=$(this).closest('tr').attr('data-id');
		changeAmount(goods_id,$(this).val());

	});
	var changeAmount=function (goods_id,amount) {
		$.post('changeamount.html',{goods_id:goods_id,amount:amount},function () {
        })
    };

});