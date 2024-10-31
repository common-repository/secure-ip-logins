jQuery(function($){
	$(window).load(function(){
		$('.siplbks_ip_input').mask('099.099.099.099');
	});
	$(document).ready(function(){
		var next = 1;
		var ipcount = siplbks_js_var_obj.ipListCount;
		$(".add-more").click(function(e){
			e.preventDefault();
			console.log(ipcount +"<" + siplbks_js_var_obj.allowedIPs);
			if(parseInt(ipcount) < parseInt(siplbks_js_var_obj.allowedIPs)){
				$(".iplist-input-list").append(
					'<div class="ilist-div">'+
						'<input pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" autocomplete="off" class="input siplbks_ip_input" id="siplbks_iplist_key'+ipcount+'" name="siplbks_option[siplbks_iplist_key][]" type="text" value="" placeholder="xxx.xxx.xxx.xxx" />'+
						'<span class="remove-me"><i class="dashicons dashicons-trash"></i></span>'+
					'</div>'
				);
				$('#siplbks_iplist_key'+ipcount).mask('099.099.099.099');
				ipcount++;
			}
			else{
				tb_show("Whitelisted IPs Limit Exceeds", "#TB_inline?amp;inlineId=go-premium-popup");

			}
		});
		$('.iplist-input-list').on('click', '.remove-me', function(e){
			e.preventDefault();
			$(this).closest(".ilist-div").remove();
			ipcount--;
		});
		

		
	});
});