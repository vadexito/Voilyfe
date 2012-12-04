window.HiddenFormView = Backbone.View.extend({
    
    initialize: function(){
        
        var self = this;
        
        this.$el = $('#'+this.options.input.get('propertyName') 
              +'_itemGroup_form_page').find('div[data-role=content]').first().clone(),
        
        this.model.on('remove',function(){
            self.close();
        })
    },
    
    model:Tag,
    
    render: function() {
        var self = this;
        
        //remove already existing id's'
        this.$el.removeAttr('data-role').removeAttr('id').hide();
        
        //prepare attribute name for sending form
        this.$el.find('input,select').each(function(){
            
            $(this).attr(
                'name',
                self.options.input.get('formElementName')
                    +'['+self.model.get('id')+'][new]['+$(this).attr('data-property-name')+']'
                    ).removeAttr('class').removeAttr('id');
        });
        
        return this.$el;
    }
});


