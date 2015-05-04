if(window.jQuery) {
	var j = jQuery.noConflict();
	j.fn.serializeObject = function() {
    var o = {};
    j(this).find('input[type="hidden"], input[type="text"], input[type="password"], input[type="checkbox"]:checked, input[type="radio"]:checked, select').each(function() {
        if (j(this).attr('type') == 'hidden') { //if checkbox is checked do not take the hidden field
            var jparent = j(this).parent();
            var jchb = jparent.find('input[type="checkbox"][name="' + this.name.replace(/\[/g, '\[').replace(/\]/g, '\]') + '"]');
            if (jchb != null) {
                if (jchb.prop('checked')) return;
            }
        }
        if (this.name === null || this.name === undefined || this.name === '') return;
        var elemValue = null;
        if (j(this).is('select')) elemValue = j(this).find('option:selected').val();
        else elemValue = this.value;
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(elemValue || '');
        } else {
            o[this.name] = elemValue || '';
        }
    });
    return o;
}
jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}
	j("document").ready(function() {
		j('.navColorPicker').each(function(i, e) {
			var color_ = j(e).data("color");
			var d_ = j(e).find('div');
			j(d_).css('backgroundColor', color_);
			j(e).ColorPicker({
				color: color_,
				onShow: function(c) {
					j(c).fadeIn(500);
					return false;
				},
				onHide: function(c) {
					j(c).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					var d = j(e).find('div');
					var cc = j(e).data("index");
					var inp = j("input[data-index="+cc+"]");
					if(j(inp).length > -1) {
						j(inp).val('#' + hex);
					}
					j(e).data("color", '#' + hex);
					if(d) j(d).css('backgroundColor', '#' + hex);
				}
			});
		});
		j('form#navcolorizer-settings').submit(function(event) {
			/*var formData = j(this).serializeObject();
			var pickers = j(this).find(".navColorPicker");
			console.log(formData);
			if(j(pickers).length > -1) {
				var pvs = [];
				j(pickers).each(function(i, e) {
					pvs.push(j(e).data("color"));
				});
				formData.colors = pvs;
				console.log(formData);
			}
			formData = j.param(formData);
			j.ajax({url: j(this).attr("action"), data: formData, type: "post", success: function() { alert("Saved"); }});
			event.preventDefault();*/
			return true;
		});
	});
}
else { alert("Menu Colorizer plugin says: No jquery present."); }