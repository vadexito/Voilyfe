window.SubformView = Backbone.View.extend({
    initialize: function(){
        this.model.on('change:inputTag',this.updateSubform,this);
        this.$el.find('input').addClass('form-template');
    },
    
    events:{
        "click a.menu_done": "saveTag"
    },
    
    updateSubform: function(){

        //reset all input values (not the hidden one)
        this.$el.find('input[type=text]').val('');

        //activate readonly and fullfill name input
        this.$el.find('input[data-item-name=name]').val(this.model.get('inputTag')).attr('readonly','readonly');
    },
    
    saveTag: function(){
        
        var tag = new Tag({
            text: this.$el.find('input[data-item-name=name]').val(),
            id: this.collection.length
        });
        
        this.collection.addTag(
            tag,
            'addTag:'+this.model.get('propertyName')
        );
            
            
        //adding hidden formular
        this.model.get('tagsContainer').append(new HiddenFormView({
            model: tag,
            formElementName: this.model.get('formElementName'),
            el:$('#'+this.model.get('propertyName') 
              +'_itemGroup_form_page').find('div[data-role=content]').first().clone()
        }).render()); 
    }
});

