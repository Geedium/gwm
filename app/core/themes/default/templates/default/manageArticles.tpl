<section>
    <div>
        <h1>
            <i class="fas fa-newspaper"></i>
            Articles
        </h1>

        <table border>
            <tr>
                <th><input type="checkbox"></th>
                <th>Title</th>
                <th>Content</th>
                <th>Date</th>
            </tr>
            <tr>
                <td><input type="checkbox"></td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
            </tr>
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
                <input class="btn" type="submit" value="Submit">
            </div>
        </form>
    </div>
</section>