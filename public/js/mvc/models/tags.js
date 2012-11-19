window.Tags = Backbone.Collection.extend({
    
    initialize: function() {
        
         _.extend(this,Backbone.Events);
    },
    
    model: Tag,
    
    addTag: function(tag,eventToTrigger){
        this.add(tag);
        this.trigger(
            eventToTrigger,
            tag
        );
    }
    
});


