Backbone.View.prototype.close = function(){
    
    //hook for before closing
    if (this.beforeClose){
        this.beforeClose();
    }
    
    //close and unbind
    this.remove();
    this.unbind();
}


