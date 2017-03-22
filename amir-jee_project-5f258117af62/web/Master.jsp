<%--
  Created by IntelliJ IDEA.
  User: Mokh
  Date: 01/12/2016
  Time: 21:45
  To change this template use File | Settings | File Templates.
--%>
<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title><!-- change -->

    <!-- Styles -->
    <link rel="stylesheet" href="/public/css/front.css">
    <link rel="stylesheet" href="/public/css/frontend.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" rel="stylesheet">


    <style>
        .datepicker{z-index:1151 !important;}
        .nav-tabs-custom>.nav-tabs>li.active {
            border-top-color: #dd4b39;
        }
        .red-tooltip + .tooltip > .tooltip-inner {background-color: #dd4b39;}
        .blue-tooltip + .tooltip > .tooltip-inner {background-color: #337ab7;}
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="http://formvalidation.io/vendor/formvalidation/css/formValidation.min.css"></script>

    <![endif]-->
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

    <header class="main-header">

        <!-- Logo -->
        <a href="/dashboard" class="logo">
            <!-- mini logo for sidebar mini 50x30 pixels -->
            <span class="logo-mini"></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"></span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header">MENU</li>
                <li class="treeview">
                    <a href="/dashboard">
                        <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="treeview">
                    <a href="/Events">
                        <i class="fa fa-table"></i>
                        <span>Evénements</span>
                        <span>
              <span class="label label-primary pull-right blue-tooltip" data-toggle="tooltip" data-placement="top" title="Prochainement">{{$upcoming_events_nbr}}</span>
              <span class="label label-danger pull-right red-tooltip" data-toggle="tooltip" data-placement="top" title="En Cours">{{$current_events_nbr}}</span>
            </span>
                    </a>
                </li>
                <li class="">
                    <a href="/Users">
                        <i class="fa fa-users"></i>
                        <span>Gestion Des Utilisateurs</span>
                    </a>
                </li>
                <li class="treeview">
                    <a href="">
                        <i class="fa fa-user"></i>
                        <span>Gestion De l'equipement</span>
                        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
                 </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/Items"><i class="fa fa-keyboard-o"></i>Equipement</a></li>
                        <li><a href="/Categories"><i class="fa fa-asterisk"></i>Catégories</a></li>
                    </ul>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Liste des Catégories :
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Catégories</a></li>
                <li class="active">Catégories</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="box box-danger">

                <div class="box-header">
                    <h3 class="box-title">Liste Des Catégories</h3>
                    <div class="box-tools pull-right">

                        <div class="pull-right mb-10">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModal">Créer une Catégorie</button>
                        </div><!--pull right-->
                    </div>
                </div>
                <!-- /.box-header -->

                <!-- Modal for category creation -->
                <div class="modal fade" id="myModal" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Nouvelle Catégorie</h4>
                            </div>
                            <div class="modal-body" id="create-category-modal-body">
                                <form id="categoryForm" method="post" class="form-horizontal" action="/createCategory">
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Nom</label>
                                        <div class="col-xs-5">
                                            <input type="text" class="form-control" name="name" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Description Courte</label>
                                        <div class="col-xs-5">
                                            <input type="text" class="form-control" name="shortDesc" id="shortDesc"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Description</label>
                                        <div class="col-xs-5">
                                            <textarea id="desc" class="form-control" name="description" placeholder="Veuillez entrer une description"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-5 col-xs-offset-3">
                                            <button type="submit" class="btn btn-primary" id="add-category-submit">valider</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal" id="modal-closeCreate">Annuler</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / .Modal -->

                <div class="box-body">

                    <table id="tab" class="table table-hover" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description Courte</th>
                            <th>Crée Le</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description Courte</th>
                            <th>Crée Le</th>
                            <th>Options</th>
                        </tr>
                        </tfoot>
                        <tbody class="table-hover">
                        <%String values[][] = (String[][])request.getAttribute("data");
                            for (int i = 0 ; i<values.length;i++){%>
                        <tr>
                            <td ><%out.print(values[i][0]);%></td>
                            <td onclick="window.location='/Category/<%out.print(values[i][0]);%>';" style="cursor: pointer;"><%out.print(values[i][1]);%></td>
                            <td onclick="window.location='/Category/<%out.print(values[i][0]);%>';" style="cursor: pointer;"><%out.print(values[i][3]);%></td>
                            <td onclick="window.location='/Category/<%out.print(values[i][0]);%>';" style="cursor: pointer;"><%=values[i][4]%></td>
                            <td>
                                <div class="btn-group">
                                    <span data-toggle="modal" data-target="#categoryModal-update">
                                        <a class="btn btn-xs btn-info"  onclick="showUpdateCategory(<%out.print(values[i][0]);%>)"><i class="fa fa-refresh" data-toggle="tooltip" data-placement="top" title="Mettre à Jour"></i></a>
                                    </span>
                                </div>

                                <div class="btn-group">
                                    <a class="btn btn-xs btn-danger" style="cursor:pointer;"  onclick="deleteCategory(<%out.print(values[i][0]);%>)"><i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="Supprimer"></i>
                                        <form action="Category/<%out.print(values[i][0]);%>/Delete" method="POST" id="deleteForm-<%out.print(values[i][0]);%>" style="display:none">
                                        </form>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <%}%>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <!-- Modal To Be Filled With Category Data For Updates-->
            <div class="modal fade" id="categoryModal-update" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body" id="update-category-modal-body">
                            <form id="categoryFormUpdate" method="POST" class="form-horizontal" action="">

                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Nom</label>
                                    <div class="col-xs-5">
                                        <input type="text" class="form-control" name="nameUpdate" id="nameUpdate" value=""/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Description Courte</label>
                                    <div class="col-xs-5">
                                        <input type="text" class="form-control" name="shortDescUpdate" id="shortDescUpdate" value=""/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Description</label>
                                    <div class="col-xs-5">
                                        <textarea id="descUpdate" class="form-control" name="descriptionUpdate" placeholder="Veuillez entrer une description"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-5 col-xs-offset-3">
                                        <button type="submit" class="btn btn-primary" id="update-category-submit">valider</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" id="modal-closeUpdate">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / .modal -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong>Copyrights &copy; 2016 <a href="#">Fandouli Mokhtar & Dhibi Amir</a>.</strong> All rights
        reserved.
    </footer>

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<script src="/public/js/libs.js"></script>

<script>
    $(document).ready(function() {

        $('#tab')
                .DataTable({
                    "language": {
                        "url": "/public/JSON/French.json"

                    },
                    "processing": true,
                    "bProcessing" : true
                });

        $('#categoryFormUpdate')
                .formValidation({
                    framework: 'bootstrap',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        nameUpdate: {
                            validators: {
                                notEmpty: {
                                    message: 'Le nom est obligatoire.'
                                }
                            }
                        },
                        shortDescUpdate: {
                            validators: {
                                notEmpty: {
                                    message: 'Veuillez saisir une courte description.'
                                }
                            }
                        },
                        descriptionUpdate: {
                            validators: {
                                notEmpty: {
                                    message: 'Veuillez saisir une description.'
                                }
                            }
                        }
                    }
                });

        $("#update-category-submit").click(function(event){
            event.preventDefault();
            $("#update-category-modal-body")

                    .append(
                            '<center><img src="/public/img/loading.gif" id="loading" style="display:none;right:50%;" /></center>'
                    );
            var loading = document.getElementById("loading");
            loading.style.display = "block";
            document.getElementById("categoryFormUpdate").style.display = "none";
            var $form = $("#categoryFormUpdate"),
                    url = $form.attr('action');
            $.post(url, $form.serialize()).done(function () {
                $("#modal-closeUpdate").click();
                swal("", "Catégorie mise à jour!", "success");
                setTimeout(function() { location.reload(true); }, 1000);
            });
        });

        $("#descUpdate")
                .focus(function() {
                    if (this.value === this.defaultValue) {
                        this.value = '';
                    }
                })
                .blur(function() {
                    if (this.value === '') {
                        this.value = this.defaultValue;
                    }
                });

        $("#shortDescUpdate")
                .focus(function() {
                    if (this.value === this.defaultValue) {
                        this.value = '';
                    }
                })
                .blur(function() {
                    if (this.value === '') {
                        this.value = this.defaultValue;
                    }
                });

        $('#categoryForm')
                .formValidation({
                    framework: 'bootstrap',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        name: {
                            validators: {
                                notEmpty: {
                                    message: 'Le nom est obligatoire.'
                                }
                            }
                        },
                        description: {
                            validators: {
                                notEmpty: {
                                    message: 'La courte description est obligatoire.'
                                }
                            }
                        },
                        shortDesc: {
                            validators: {
                                notEmpty: {
                                    message: 'La description est obligatoire.'
                                }
                            }
                        }
                    }
                });

        $("#add-category-submit").click(function(event){
            event.preventDefault();
            $("#create-category-modal-body")

                    .append(
                            '<center><img src="/public/img/loading.gif" id="loading" style="display:none;right:50%;" /></center>'
                    );
            var loading = document.getElementById("loading");
            loading.style.display = "block";
            document.getElementById("categoryForm").style.display = "none";
            var $form = $("#categoryForm"),
                    url = $form.attr('action');
            $.post(url, $form.serialize()).done(function () {
                console.log($form.serialize());
                $("#modal-closeCreate").click();
                swal("", "Catégorie créée!", "success");
                setTimeout(function() { location.reload(true); }, 1000);
            });
        });

        $("#description")
                .focus(function() {
                    if (this.value === this.defaultValue) {
                        this.value = '';
                    }
                })
                .blur(function() {
                    if (this.value === '') {
                        this.value = this.defaultValue;
                    }
                });

        $("#shortDesc")
                .focus(function() {
                    if (this.value === this.defaultValue) {
                        this.value = '';
                    }
                })
                .blur(function() {
                    if (this.value === '') {
                        this.value = this.defaultValue;
                    }
                });
    });

    function showUpdateCategory(str) {
        $("#update-category-modal-body")

                .append(
                        '<center><img src="/public/img/loading.gif" id="loading" style="display:none;right:50%;" /></center>'
                );
        var loading = document.getElementById("loading");
        loading.style.display = "block";
        document.getElementById("categoryFormUpdate").style.display = "none";

        if (str == "") {
            document.getElementById("update-category-modal-body").innerHTML = "";
            return;
        } else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

                    var data = JSON.parse(xmlhttp.responseText);

                    $("#nameUpdate").val(data.name);
                    $("#shortDescUpdate").val(data.shortDesc);
                    $("#descUpdate").val(data.description);
                    $("#categoryFormUpdate").attr('action', '/Category/'+data.id+'/Edit ');
                    loading.style.display = "none";
                    document.getElementById("categoryFormUpdate").style.display = "block";

                }
            };
            xmlhttp.open("GET","Category/"+str+"/Edit",true);
            xmlhttp.send();

        }

    }

    function deleteCategory(str){

        swal({
                    title: "Etes vous sure de vouloir supprimer cette catégorie ?",
                    text: "Continuer?",
                    type: "warning",
                    showLoaderOnConfirm: true,
                    showCancelButton: true,
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: "Non, annuler!",
                    closeOnConfirm: false,
                    closeOnCancel: false

                },
                function (isConfirm) {
                    if (isConfirm) {
                        var formID="#deleteForm-"+str;
                        var $form = $(formID);
                        var url = $form.attr('action');
                        $.post(url, $form.serialize()).done(function () {
                            swal("Suppprimé!", "Catégorie supprimée!", "success");
                            setTimeout(function() { location.reload(true); }, 1000);
                        });

                    } else {
                        swal("Annulé", "Votre catégorie n\'a pas été supprimée", "error");
                    }
                }
        );


    }

</script>

</body>
</html>
