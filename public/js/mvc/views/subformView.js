window.SubformView = Backbone.View.extend({
    
    initialize: function(){
        this.prepareSubform();
        this.$el.find('input').addClass('form-template');
    },
    
    events:{
        "click a.menu_done": "generateTag"
    },
    
    prepareSubform: function(){

        //reset all input values (not the hidden one)
        this.$el.find('input[type=text]').val('');
        
        //activate readonly and fullfill name input
        this.$el.find('input[data-item-name=name]').val(this.options.tag.get('text')).attr('readonly','readonly');
    },
    
    generateTag: function(){
            
        //if only one element is allowed, we replace it if already choosen
        if ((this.collection.length > 0) && (this.model.get('multitag') == false)){
            
            this.collection.remove(this.collection.get(0));
        }
        
        this.collection.add(this.options.tag);
        
        //adding hidden formular
        this.model.get('tagsContainer').append(new HiddenFormView({
            model: this.options.tag,
            input: this.model
        }).render()); 
    }
});

