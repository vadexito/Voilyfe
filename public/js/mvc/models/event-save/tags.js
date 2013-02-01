window.Tags = Backbone.Collection.extend({
    
    initialize: function() {
        
         _.extend(this,Backbone.Events);
    },
    
    model: Tag,
    
    createTag: function(text,valueForInput){
        
        if (!(this.validateTag(text))){
            return false;
        }
        
        var tag = new Tag();
        tag.set({
            text:text,
            id: this.length,
            valueForInput:valueForInput
        });
        
        return tag;
    },
    
    validateTag: function(value){
        // the value is not already tagged
        var values = this.pluck("text");
        
        return ($.inArray(value.toLowerCase(),
            values.join("`").toLowerCase().split("`")) === -1);
    }
});


