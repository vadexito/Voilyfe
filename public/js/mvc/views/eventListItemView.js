window.EventListItemView = Backbone.View.extend({
    
    tagName:'li',
    className :'event-line',
    
    initialize: function(){
        
        this.template = _.template( $("#event-line-template").html());
       
        
    },
    
    render: function(){
       
        var specificProperties = [];
        _.each(this.model.get('specificProperties'),function(el){
                if (el.value){
                    specificProperties.push(el.value); 
                }
        });
        var commonProperties = [];
        var date;
        _.each(this.model.get('commonProperties'),function(el,item){
                if (item == "date"){
                    date = el;
                } else if (el.value) {
                    commonProperties.push(el.value);
                }
        });
       
        this.model.set('title',date+' - '+this.model.get('title'))
        this.model.set('specificProperties',specificProperties.join(', '));
        this.model.set('commonProperties',commonProperties.join(', '));
        this.$el.html( this.template (this.model.toJSON()) );
        return this.el;
       
    }
    
});

