<template>
	<nv-header></nv-header>
	<div class="artlist">
		<ul class="artlistTab clearfix">
			<li v-for="item in itemTab" :class="{'on':initIndex === $index}" @click="changeTab($index)">{{item.title}}</li>
		</ul>
		<div class="artlistCon">
			<div v-for="art in artlist" class="artitem clearfix" v-link="{name:'article',params:{id:art.id}}">
				<a class="avatar" href="javascript:void(0);">
					<img :src="art.member.avatar_large" :alt="art.member.username" />
				</a>
				<div class="art-inf">
					<a class="title">{{art.title}}</a><br/>
					<em>{{art.node.title}}</em>
					<span class="rp-author"><i>By • </i>{{art.member.username}}</span>
					<span class="rp-count">{{art.replies}}</span>
					<span class="last-time">{{art.created | getLastTime }}</span>
				</div>
			</div>
		</div>
		<div class="loadingbox" v-show="showLoading">
			<div class="loading"></div>
		</div>
	</div>
	<nv-top></nv-top>
</template>
<script>
import nv_header from '../components/header'
// var $=Zepto;
export default {
	data : function() {
		return { //??
			initIndex : 0,
			scroll : true,
			showLoading : false,
			itemTab : [
				{'title' : '最新', 'type' : 'topics_latest'},
				{'title' : '热门', 'type' : 'topics_hot'},
				{'title' : '程序员', 'type' : 'topics_show_programmer'},
				{'title' : '分享', 'type' : 'topics_show_share'},
				{'title' : '问与答', 'type' : 'topics_show_qna'}
			],
			artlist : [],
			searchKey : {
				page : 1,
				limit : 20, //每页加载20条
				tab : 'topics_latest' //分页 有all ask share job good
			}
		}
	},
	ready: function() {
		// console.log($('.artlistTab').html());
		this.gerArtlist(this.initIndex);
		$(window).on('scroll',() => {
			this.scrollArtlist();
		});
	},
	methods: {
		changeTab: function(index){
			this.initIndex = index;
			this.searchKey.tab = this.itemTab[index].type;
			this.artlist = [];
			this.searchKey.limit = 20;
			this.gerArtlist(this.initIndex);
		},
		// 获取数据方法
		gerArtlist : function() {
			let rqdata = $.param(this.searchKey); //??? let
			$.get('http://192.168.1.110:6067/api/?' + rqdata, (data) => {
				if(data.success){
					this.artlist = data['data'];
					this.scroll = true;
					this.showLoading = false;
				}
			})
		},
		// 超过滚动获取数据方法
		scrollArtlist : function() {
			if(this.scroll){
				let totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
				if ($(document).height() <= totalheight + 200) {
					this.scroll = false;
					this.searchKey.limit += 20;
					this.showLoading = true;
					setTimeout(() => {
						this.gerArtlist();
					}, 2000)
				}
			}
		}
	},
	components : {
		'nv-header' : nv_header,
		'nv-top' : require('../components/returnTop') //方式二
	}
}
</script>
<style lang="sass">
	.artlist{
		overflow: hidden;
		margin: 0 5px;
		.artlistTab {
			margin: 0 auto 10px;
			width: 100%;
			height: 3rem;
			background: #F6F6F6;
			border-radius: 7px;
			li {
				float: left;
				width: 15%;
				height: 2rem;
				margin: 0.5rem 0 0 4%;
				color: #778087;
				text-align: center;
				line-height: 2rem;
			}
			.on {
				background: #aab0c6;
				color: #fff;
				border-radius: 5px;
			}
		}
		.artlistCon {
			width: 100%;
			.artitem {
				position: relative;
				width: 100%;
				/*height: 3rem;*/
				padding: 10px;
				background: #fff;
				border: 1px solid #e1e1e1;
				.avatar {
					display: inline-block;
					position: absolute;
					left: 10px;
					top: 10px;
					width: 2rem;
					height: 2rem;
					img {
						display: inline-block;
						width: 100%;
						height: 100%;
					}
				}
				.art-inf {
					position: relative;
					width: 80%;
					height: 100%;
					padding-left: 2.5rem;
					a {
						display: inline-block;
						overflow: hidden;
						width: 90%;
						height: 1.2rem;
						color: #000;
						font-size: 1rem;
						line-height: 1.2rem;
						white-space: nowrap;
						text-overflow: ellipsis;
					}
					span, em {
						display: inline-block;
					}
					em {
						margin-top: 2%;
						padding: 2px 4px;
						background: #e5e5e5;
						border-radius: 3px;
						-webkit-border-radius: 3px;
						-moz-border-radius: 3px;
						-o-border-radius: 3px;
						color: #999;
						font-size: 0.8rem;
					}
					.put_good, .put_top {
						background: #80bd01;
						color: #fff;
					}
					.rp-author {
						margin-left:10%;
						margin-left: 0%;
						font-size: 12px;
						padding: 2px 4px;
						i{
							color:#ccc;
						}
					}
					.rp-count {
						position: absolute;
						right: -4%;
						top: 0px;
						font-size: 0.7em;
						line-height: 1.3em;
						font-weight: bold;
						color: white;
						background-color: #aab0c6;
						display: inline-block;
						padding: 2px 10px 2px 10px;
						-moz-border-radius: 12px;
						-webkit-border-radius: 12px;
						border-radius: 12px;
						text-decoration: none;
					}
					.last-time {
						position: absolute;
						right: -5%;
						bottom: 0px;
						font-size: 12px;
						padding: 2px 4px;
					}
				}
			}

		}
		.loadingbox {
			position: relative;
			width: 100%;
			height: 40px;
			.loading {
				position: absolute;
				width: 25px;
				height: 25px;
				left: 0px;
				top: 0px;
				right: 0px;
				bottom: 0px;
				margin: auto;
				background: url('../img/loading.gif') no-repeat;
				background-size: 100%;
			}
		}
	}
</style>