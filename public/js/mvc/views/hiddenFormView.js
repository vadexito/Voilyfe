window.HiddenFormView = Backbone.View.extend({
    
    tagName: "div",
    className: "hidden-form-item-group",
    model:Tag,
    
    
    initialize: function(){
        
        var self = this;
        var $formTemplateFull = $('#'+this.options.input.get('propertyName') 
              +'_itemGroup_form_page').find('div[data-role=content]');
        
        $formTemplateFull.find('input,select').each(function(){
            
            var newElement = $(this).clone();
            newElement.attr('name',self.options.input.get('formElementName')
                +'['+self.model.get('id')+'][new]['+$(this).attr('data-property-name')+']'
                ).removeAttr('class').removeAttr('id').val($(this).val());
                    
            self.$el.append(newElement);
        });
        
        this.model.on('remove',function(){
            self.close();
        })
    },
    
    render: function() {
        return this.$el.hide();
    }
});


