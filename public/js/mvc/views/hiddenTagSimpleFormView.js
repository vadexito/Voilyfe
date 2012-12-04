window.HiddenTagSimpleFormView = Backbone.View.extend({
    
    initialize: function(){
        
        var self = this;
        if (this.options.isNewTag){
            var nameprefix = this.options.formElementName
                +'['+this.model.get('id')+']'
                + '[new]';
           this.$el.attr('name',nameprefix+'[value]').val(this.model.get('text'));
           var value = this.options.itemId;
           this.options.hiddenElement = $('<input type="hidden" name="'+nameprefix+'[itemId]" value="'+value+'"/>');
        } else {
            name = this.options.formElementName+'['+this.model.get('id')+']'
            + '[id]';
            this.$el.attr('name',name).val(this.model.get('valueForInput'));
       }
       
       this.options.tagsContainer.append(this.$el.after(this.options.hiddenElement));
       
       this.model.on('remove',function(){
           self.close();
       })
       
    },
    
    model:Tag,
    tagName: "input",
    attributes:{
        'type':"text",
        'readonly':"readonly"
    },
    
    render: function() {
        
        this.$el.hide();
        return this.el;
    }
});


