window.TagView = Backbone.View.extend({
    tagName: "a",
    className: "li-tags",
    model:Tag,
    attributes:{
        'data-inline':"true",
        'data-theme':"e",
        'data-role':"button"
    },
    events:{
      'click': 'deleteIcon'
    },

    deleteIcon: function (e){
        
        if (this.$el.hasClass('hasDeleteButton')){
            this.collection.remove(this.model);
            this.close();
        } else {
            this.$el.addClass('hasDeleteButton');
            this.$el.buttonMarkup({ icon: "delete" });
        }
        
        return false;
    },
    
    
    
    render: function() {
        this.$el.html(this.model.get("text"));
        
        return this.el;
    }
});


