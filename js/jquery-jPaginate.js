/*jPaginate
jQuery plugin (http://jquery.com/)
jQuery => Copyright 2010, John Resig
jPaginate => Copyright 2011, Adrien Guéret
Dual licensed under the MIT or GPL Version 2 licenses.
http://jquery.org/license
Last update: 28/07/2011*/
(function($)
{
	$.fn.jPaginate=function(options)
	{
		var defaults=
		{
			'max': 5,
			'page': 1,
			'links': 'text'
		};
		
		var parameters=$.extend(defaults,options);
		//We check the given values
		parameters.max=Math.max(1,parameters.max);
		parameters.page=Math.max(1,parameters.page);
		
		return this.each(function()
		{
			var table=$(this);
			if(table.is('table'))
			{
				//We get the container (table or tbody)
				var container=table.find('tbody');
				
				if(container.length==0)
				{
					container=table;
				}
				
				var nbr_pages=0;
				var colspan=0;
				container.find('tr').each(function(i)
				{
					var row=$(this);
					if(i%parameters.max==0)
					{
						nbr_pages++;
					}
					
					//We add class to rows and we hide them
					row.addClass('jPaginate-row-'+nbr_pages);
					
					if(nbr_pages!=parameters.page)
					{
						row.hide();
					}
					
					/* We also get the number of cells in a row
					(check the first one is sufficient) */
					if(i==0)
					{
						row.find('td,th').each(function()
						{
							//If the cell has a colspan attribute, we add its value
							colspan+=$(this).attr('colspan')!=undefined?1+parseInt($(this).attr('colspan'),10):1;
						});
					}
				});
				
				//We add the footer
				var tfoot=table.find('tfoot');
				if(tfoot.length==0)
				{
					tfoot=$('<tfoot></tfoot>');
					tfoot.appendTo(table);
				}
				var th=$('<th colspan="'+colspan+'"></th>');
				$('<tr class="jPaginate-links-row"></tr>').append(th).appendTo(tfoot);
				
				//We generate the links if we have to
				if(nbr_pages>1)
				{
					function showPage(num)
					{
						container.find('tr').hide();
						container.find('.jPaginate-row-'+num).css('display','table-row');
					}
					
					var linksHTML='';
					switch(parameters.links)
					{
						case 'select':
							for(var i=1; i<=nbr_pages; i++)
							{
								linksHTML+='<option value="'+i+'"'+
								'class="jPaginate-link-option'+
								(i==parameters.page?' jPaginate-link-option-selected" selected="selected':'')+
								'">'+i+'</option>';
							}
							th.html('<select class="jPaginate-link-select">'+linksHTML+'</select>');
							th.find('select').change(function()
							{
								var select=$(this);
								select.find('.jPaginate-link-option').removeClass('jPaginate-link-option-selected');
								select.find('.jPaginate-link-option[value='+select.val()+']').addClass('jPaginate-link-option-selected');
								showPage(select.val());
							});
						break;
						
						case 'buttons':
							th.html('<input class="jPaginate-link-button-first" type="button" value="&laquo;">'+
							'<input class="jPaginate-link-button-previous" type="button" value="&lt;">'+
							'<input class="jPaginate-link-input" type="text" value="'+parameters.page+'">'+
							'<input class="jPaginate-link-button-next" type="button" value="&gt;">'+
							'<input class="jPaginate-link-button-last" type="button" value="&raquo;">');
							
							var input=th.find('.jPaginate-link-input');
							
							input.change(function()
							{
								var $this=$(this);
								var val=isNaN($this.val())?1:$this.val();
								$this.val(Math.max(1,Math.min(nbr_pages,val)));
								showPage($this.val());
							});
							
							th.find('.jPaginate-link-button-first').click(function()
							{
								input.val(1).change();
							});
							
							th.find('.jPaginate-link-button-last').click(function()
							{
								input.val(nbr_pages).change();
							});
							
							th.find('.jPaginate-link-button-previous').click(function()
							{
								input.val(parseInt(input.val(),10)-1).change();
							});
							
							th.find('.jPaginate-link-button-next').click(function()
							{
								input.val(parseInt(input.val(),10)+1).change();
							});
						break;
						
						default:
							for(var i=1; i<=nbr_pages; i++)
							{
								linksHTML+='<a class="jPaginate-link-text'+(i==parameters.page?' jPaginate-link-text-active':'')+'" href="#">'+i+'</a> ';
							}
							th.html(linksHTML);
							
							$('.jPaginate-link-text').click(function(e)
							{
								e.preventDefault();								
								th.find('a').removeClass('jPaginate-link-text-active');
								$(this).addClass('jPaginate-link-text-active');
								showPage($(this).text());
							});
						break;
					}
				}
			}
		});
	};
})(jQuery);
