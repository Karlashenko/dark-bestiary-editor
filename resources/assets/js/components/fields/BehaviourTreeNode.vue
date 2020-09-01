<template>
    <div style="padding: 20px; white-space: nowrap; overflow: auto;">
        <div id="tree"></div>

        <context-menu ref="context" style="margin: 0; padding: 0;">
            <ul class="dropdown-menu" style="display: block">
                <li class="menu-item" v-if="selected">
                    <a @click="editData(selected)">
                        <span class="icon icon-edit"></span> Edit
                    </a>
                </li>

                <div class="dropdown-divider"></div>

                <li class="menu-item dropdown dropdown-submenu" v-for="(types, group) in window.library.nodeGroupsDictionary">
                    <a class="dropdown-toggle" :data-toggle="'#dropdown-' + group">
                        <span :class="'icon icon-' + getIconClass(group, '')"></span>{{ group }} <span class="glyphicon glyphicon-triangle-right pull-right"></span>
                    </a>

                    <ul :id="'#dropdown-' + group" class="dropdown-menu">
                        <li class="menu-item" v-for="type in types">
                            <a @click="add(group, type)">
                                <span :class="'icon icon-' + getIconClass(group, type)"></span> {{ type }}
                            </a>
                        </li>
                    </ul>
                </li>

                <div class="dropdown-divider"></div>

                <li class="menu-item" v-if="selected && selected.parent">
                    <a @click="remove()">
                        <span class="icon icon-remove"></span> Remove
                    </a>
                </li>
            </ul>
        </context-menu>

        <div class="modal fade" :id="modalId" v-if="temp" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document" style="width: 1050px; text-align: center;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span :class="'icon icon-' + getIconClass(temp.group, temp.type)"></span> {{ temp.type }}
                        </h5>
                    </div>

                    <div class="modal-body">
                        <table class="form-table">
                            <tr>
                                <td>Id</td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" v-model="temp.id" disabled>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Type</td>
                                <td>
                                    <select class="form-control" data-live-search="true" v-model="temp.type">
                                        <optgroup v-for="(group, label) in window.library.nodeGroupsDictionary" :label="label">
                                            <option v-for="type in group" :value="type" :selected="type === temp.type">
                                                {{ type }}
                                            </option>
                                        </optgroup>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Name</td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" v-model="temp.name">
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Comment</td>
                                <td>
                                    <input type="text" class="form-control" v-model="temp.comment">
                                </td>
                            </tr>
                        </table>

                        <hr>

                        <table class="form-table">
                            <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value Type</th>
                                <th>Value</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="property in temp.properties">
                                <td>
                                    <input type="text" class="form-control" v-model="property.key">
                                </td>

                                <td>
                                    <select class="form-control" data-live-search="true" v-model="property.type">
                                        <option v-for="type in ['String', 'Number', 'Skill', 'Template', 'ResourceType']" :value="type" :selected="type === property.type">
                                            {{ type }}
                                        </option>
                                    </select>
                                </td>

                                <td v-if="property.type === 'String'">
                                    <input type="text" class="form-control" v-model="property.value">
                                </td>

                                <td v-if="property.type === 'Number'">
                                    <input type="number" class="form-control" v-model="property.value">
                                </td>

                                <td v-if="property.type === 'Skill'">
                                    <select class="form-control" data-live-search="true" v-model="property.value">
                                        <option v-for="skill in skills" :value="skill.id" :selected="skill.id === property.value">
                                            {{ skill.prefix + ' - ' + window.getPropertyByPath(skill, "name_i18n.en|label") + ' (' + window.getPropertyByPath(skill, "effect.name") + ')' }}
                                        </option>
                                    </select>
                                </td>

                                <td v-if="property.type === 'Template'">
                                    <select class="form-control" data-live-search="true" v-model="property.value">
                                        <option v-for="tree in trees.filter(tree => tree.type === 'Template')" :value="tree.id" :selected="tree.id === property.value">
                                            {{ tree.name }}
                                        </option>
                                    </select>
                                </td>

                                <td v-if="property.type === 'ResourceType'">
                                    <select class="form-control" data-live-search="true" v-model="property.value">
                                        <option v-for="resourceType in window.library.resourceTypes" :value="resourceType" :selected="resourceType === property.value">
                                            {{ resourceType }}
                                        </option>
                                    </select>
                                </td>

                                <td>
                                    <button class="btn btn-default" @click="removeData(data)"><span class="glyphicon glyphicon-trash"></span></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-wide pull-left" @click="addData()"><span class="glyphicon glyphicon-plus"></span></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" @click="modalHide()">Close</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="applyData(selected)">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import contextMenu from "vue-context-menu";

    export default {
        components: {contextMenu},

        data() {
            return {
                window: window,
                modalId: undefined,
                jqtree: undefined,
                selected: undefined,
                temp: undefined,
                skills: [],
                trees: []
            };
        },

        mounted() {
            this.modalId = "tree_modal_" + this._uid;
            window.$("#" + this.modalId).modal();

            window.axios.get("/frontend/skills").then(response => {
                this.skills = response.data;
            }).then(() => {
                window.axios.get("frontend/behaviour_trees?type=Tree,Template").then(response => {
                    this.trees = response.data;
                });
            }).then(() => {
                this.jqtree = $("#tree");

                this.jqtree.tree({
                    data: [this.value],
                    autoOpen: true,
                    dragAndDrop: true,
                    useContextMenu: true,
                    selectable: true,
                    onCreateLi: (node, $li) => {
                        $li.find(".jqtree-title").html(this.getLabelString(node));
                        $li.find(".jqtree-title").before("<span class='icon icon-" + this.getIconClass(node.group, node.type) + "'></span>");
                        $li.find(".jqtree-title").after(this.getCommentString(node));
                        $li.find(".jqtree-title").after(this.getPropertiesString(node));
                    }
                });

                this.jqtree.on("tree.click", (event) => {
                    this.selected = event.node;
                });

                this.jqtree.on("tree.contextmenu", (event) => {
                    this.selected = event.node;

                    let selectedNodes = this.jqtree.tree("getSelectedNodes", this.selected);

                    for (let key in selectedNodes) {
                        this.jqtree.tree("removeFromSelection", selectedNodes[key]);
                    }

                    this.jqtree.tree("addToSelection", this.selected);
                    this.$refs.context.open();
                });
            });
        },

        props: ["value"],

        methods: {
            add: function (group, type) {
                window.treeIdCounter = window.treeIdCounter ? window.treeIdCounter : 1000000;
                window.treeIdCounter++;

                let fresh = {
                    id: window.treeIdCounter,
                    name: "",
                    type: type,
                    comment: "",
                    group: group,
                    data: []
                };

                this.jqtree.tree("appendNode", fresh, this.selected);
                this.jqtree.tree("openNode", this.selected);
            },

            remove: function () {
                this.jqtree.tree("removeNode", this.selected);
            },

            editData: function (node) {
                window.axios.get("frontend/behaviour_trees?type=Tree,Template").then(response => {
                    this.trees = response.data;

                    let index = -1;

                    for (let key in this.trees) {
                        if (this.trees[key].id === this.value.id) {
                            index = key;
                            break;
                        }
                    }

                    this.trees.splice(index, 1);

                    this.temp = this.breakCircularReferences(node);
                    setTimeout(this.modalShow, 100);
                });
            },

            addData: function () {
                this.temp.properties.push({key: "key", type: "String", value: "value"});
            },

            removeData: function (data) {
                let index = this.temp.properties.indexOf(data);
                this.temp.properties.splice(index, 1);
            },

            applyData: function (node) {
                Object.assign(node, this.temp);

                this.jqtree.tree("loadData", this.jqtree.tree("getTree").children);
                this.modalHide();
            },

            modalShow: function () {
                window.$("#" + this.modalId).modal("show");
            },

            modalHide: function () {
                window.$("#" + this.modalId).modal("hide");
                this.rebuild(this.getRoot());
            },

            getLabelString: function (node) {
                let string = node.type;

                if (node.name) {
                    string += "(" + node.name + ")"
                }

                return string;
            },

            getCommentString: function (node) {
                let string = node.comment;

                if (!string) {
                    return "";
                }

                return "<span style='color: #008800; vertical-align: middle;'> // " + node.comment + "</span> ";
            },

            getPropertiesString: function (node) {
                if (node.properties === undefined) {
                    node.properties = [];
                }

                if (node.properties.length === 0) {
                    return "";
                }

                let dataString = " (";

                for (let key in node.properties) {
                    let keyString = "<span style='color: #888888; font-size: 0.8em;'>" + node.properties[key].key + ":</span>";
                    let typeString = "<span style='color: #3771A9'>" + node.properties[key].type + "</span>";
                    let valueString = "";

                    if ($.isNumeric(node.properties[key].value)) {
                        valueString = "<span style='color: #DA7A2E'>" + node.properties[key].value + "</span>";

                        if (node.properties[key].type === 'Template') {
                            let template = this.trees.find(function(tree) {
                                return tree.id == node.properties[key].value;
                            });

                            if (template !== undefined) {
                                valueString = "<span style='color: #DA7A2E'>\"" + template.name + "\"</span>";
                            }
                        }

                        if (node.properties[key].type === 'Skill') {
                            let skill = this.skills.find(function(skill) {
                                return skill.id === node.properties[key].value;
                            });

                            if (skill !== undefined) {
                                valueString = "<span style='color: #DA7A2E'>\"" + window.getPropertyByPath(skill, 'name_i18n.en|label') + "\"</span>";
                            }
                        }
                    }

                    dataString += keyString + " " + typeString + " " + valueString + ", ";
                }

                dataString = dataString.substring(0, dataString.length - 2);
                dataString += ")";

                return dataString;
            },

            getIconClass: function (group, type) {
                let icon = type;

                if (!icon) {
                    icon = group;
                }

                icon = icon.toLowerCase();

                if (group === "Task" || group === "Condition") {
                    icon = group.toLowerCase();
                }

                return icon;
            },

            breakCircularReferences: function (node) {
                let fresh = Object.assign({}, node);
                fresh.parent = undefined;
                fresh.tree = undefined;
                fresh.element = undefined;

                for (let key in fresh.children) {
                    fresh.children[key] = this.breakCircularReferences(fresh.children[key]);
                }

                return fresh;
            },

            rebuild: function (root) {
                if (!this.jqtree) {
                    return;
                }

                this.jqtree.tree("loadData", [root]);
            },

            getRoot: function () {
                let root = this.jqtree.tree("getTree").children[0];
                return this.breakCircularReferences(root);
            }
        }
    };
</script>

<style scoped>
</style>
