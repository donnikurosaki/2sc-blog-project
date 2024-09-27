
document.addEventListener('DOMContentLoaded', ()=> {
    new App();
})

class App {
    constructor(){
        this.enableDropdowns()
        this.handleCommentForm()
    }

    enableDropdowns() {
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        dropdownElementList.map(function (dropdownToggleEl){
            return new Dropdown(dropdownToggleEl)
        });
    }

    handleCommentForm(){

        const commentForm = document.querySelector('form.comment-form')

        if (null == commentForm){
            return;
        }

        commentForm.addEventListener('submit', async(e) => {
            e.preventDefault();

            const response = await fetch('/comment/add', {
                method: 'POST',
                body: new FormData(e.target)
            });

            if (!response.ok ) {
                return;
            };

            const json = await response.json();
            console.log(json);
        })

        const commentList = document.querySelector('.comment-list');

        commentForm.addEventListener('submit', async (e) => {
        
        const json = await response.json();
        const commentHtml = json.message;
        
        const newComment = document.createElement('div');
        newComment.innerHTML = commentHtml;
        commentList.insertBefore(newComment, commentList.firstChild);
        
        const commentCount = document.getElementById('comment-count');
        commentCount.textContent = json.number_of_comment + ' commentaire(s)';
        });

    }

}