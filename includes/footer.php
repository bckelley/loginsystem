        <!-- Modal Login Form -->
        <div class="modal fade" id="modalUserLogin" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">User Login</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="statusMsg"></div>
                        <form action="account.php">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?PHP echo !empty($postData['email'])?$postData['email']:''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <a href="javascript:void(0);" class="" data-toggle="modal" data-target="#modalForgot">Forgot?</a>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <input type="submit" class="btn btn-success" name="loginSubmit" value="Login" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Forgot Form -->
        <div class="modal fade" id="modalForgot" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Forgot Password</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="statusMsg"></div>
                        <form action="account.php" role="form">
                            <div class="form-group">
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="userSubmit">Send</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Add and Edit Form -->
        <div class="modal fade" id="modalUserAddEdit" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                    <h4 class="modal-title">Add New User</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="statusMsg"></div>
                        <form action="account.php" role="form">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter phone no">
                            </div>
                            <input type="hidden" class="form-control" name="id" id="id"/>
                        </form>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="userSubmit">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Update the users data list
            function getUsers(){
                $.ajax({
                    type: 'POST',
                    url: 'userAction.php',
                    data: 'action_type=view',
                    success:function(html){
                        $('#userData').html(html);
                    }
                });
            }

            // Send CRUD requests to the server-side script
            function userAction(type, id){
                id = (typeof id == "undefined")?'':id;
                var userData = '', frmElement = '';
                if(type == 'add'){
                    frmElement = $("#modalUserAddEdit");
                    userData = frmElement.find('form').serialize()+'&action_type='+type+'&id='+id;
                }else if (type == 'edit'){
                    frmElement = $("#modalUserAddEdit");
                    userData = frmElement.find('form').serialize()+'&action_type='+type;
                }else{
                    frmElement = $(".row");
                    userData = 'action_type='+type+'&id='+id;
                }
                frmElement.find('.statusMsg').html('');
                $.ajax({
                    type: 'POST',
                    url: 'userAction.php',
                    dataType: 'JSON',
                    data: userData,
                    beforeSend: function(){
                        frmElement.find('form').css("opacity", "0.5");
                    },
                    success:function(resp){
                        frmElement.find('.statusMsg').html(resp.msg);
                        if(resp.status == 1){
                            if(type == 'add'){
                                frmElement.find('form')[0].reset();
                            }
                            getUsers();
                        }
                        frmElement.find('form').css("opacity", "");
                    }
                });
            }

            // Fill the user's data in the edit form
            function editUser(id){
                $.ajax({
                    type: 'POST',
                    url: 'userAction.php',
                    dataType: 'JSON',
                    data: 'action_type=data&id='+id,
                    success:function(data){
                        $('#id').val(data.id);
                        $('#name').val(data.name);
                        $('#email').val(data.email);
                        $('#phone').val(data.phone);
                    }
                });
            }

            // Actions on modal show and hidden events
            $(function(){
                $('#modalUserAddEdit').on('show.bs.modal', function(e){
                    var type = $(e.relatedTarget).attr('data-type');
                    var userFunc = "userAction('add');";
                    if(type == 'edit'){
                        userFunc = "userAction('edit');";
                        var rowId = $(e.relatedTarget).attr('rowID');
                        editUser(rowId);
                    }
                    $('#userSubmit').attr("onclick", userFunc);
                });
                
                $('#modalUserAddEdit').on('hidden.bs.modal', function(){
                    $('#userSubmit').attr("onclick", "");
                    $(this).find('form')[0].reset();
                    $(this).find('.statusMsg').html('');
                });
            });
        </script>
    </body>
</html>