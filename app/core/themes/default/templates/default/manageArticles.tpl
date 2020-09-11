<section>
    <div>
        <h1>
            <i class="fas fa-newspaper"></i>
            Articles
        </h1>

        <table border>
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Date</th>
                </tr>
            </thead>
            
            <tbody>
                <!--Articles-->
            </tbody>
        </table>

        <h3>Create Article</h3>

        <form action="/api/articles" method="post">
            <div class="form-control">
                <label for="title">Title</label>
                <input type="text" name="title" id="title">
            </div>

            <div class="form-control">
                <label for="content">Content</label>
                <textarea name="content" id="content" cols="30" rows="3"></textarea>
            </div>

            <div class="form-control">
                <input class="btn" type="submit" value="Create">
            </div>
        </form>

        <h3>Update Article</h3>

        <form action="/api/articles" method="post">
            <div class="form-control">
                <label for="ID">ID</label>
                <input type="text" name="id" id="ID">
            </div>

            <div class="form-control">
                <label for="title">Title</label>
                <input type="text" name="title" id="title">
            </div>

            <div class="form-control">
                <label for="content">Content</label>
                <textarea name="content" id="content" cols="30" rows="3"></textarea>
            </div>

            <div class="form-control">
                <input class="btn" type="submit" value="Update">
            </div>
        </form>
    </div>
</section>