window.HiddenFormView = Backbone.View.extend({
    model:Tag,
    
    render: function() {
        var self = this;
        
        //remove already existing id's'
        this.$el.removeAttr('data-role').removeAttr('id').hide();
        
        //prepare attribute name for sending form
        this.$el.find('input,select').each(function(){
            
            $(this).attr(
                'name',
                self.options.formElementName+'['+self.model.get('id')+'][new]['+$(this).attr('data-property-name')+']'
            ).removeAttr('class').removeAttr('id');
        });
        
        return this.$el;
    }
});


