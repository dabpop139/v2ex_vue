<template>
	<nv-header></nv-header>
	<div class="article">
		<div class="articlebox">
			<div class="articletitle">
				<h2>{{art.title}}</h2>
				<div>
					<span>发布于{{art.createtime | getLastTime}}</span>
					<span>作者{{art.author_name}}</span>
					<span>{{art.reply_count}}次回复</span>
					<span>{{art.visit_count}}次浏览</span>
				</div>
			</div>
			<div class="articlecontent" v-html="art.content"></div>
		</div>
		<div class="articlereplies">
			<div class="repliesti">共有<span>{{art.reply_count}}</span>条回复</div>
			<ul>
				<li v-for="reitem in replies">
					<div class="author_content clearfix">
						<img :src="reitem.member.avatar_large" :alt="reitem.member.username" />
						<span>{{reitem.member.username}}</span>
						<span class="re-time"><i>{{$index}}楼 • </i>{{reitem.created | getLastTime}}</span>
					</div>
					<div class="repliescon">
						<div class="repliescontent" v-html="reitem.content_rendered"></div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</template>
<script>
	export default {
		data : function() {
			return {
				art : {
					'title' : '',
					'content' : '',
					'createtime' : '',
					'author_name' : '',
					'author_avatar' : '',
					'visit_count' : 0,
					'reply_count' : 0
				},
				replies : [],
				articleId : ''
			}
		},
		route : {
			data (transition){
				this.articleId = transition.to.params.id;
				var get_replies = function(){
					$.get('http://192.168.1.110:6067/api/?act=replies_show_'+this.articleId,(data) => {
						if(data.success){
							this.replies=data['data'];
						}
					});
				};
				$.get('http://192.168.1.110:6067/api/?act=show_id_'+this.articleId,(d) => {
					if(d && d.data){
						// console.log(d.data);
						var D = d.data[0];
						this.art.title = D.title;
						this.art.content = D.content_rendered;
						this.art.createtime = D.created;
						this.art.author_name = D.member.username;
						this.art.author_avatar = D.member.avatar_large;
						// this.art.visit_count = D.visit_count;
						this.art.reply_count = D.replies;
						// this.replies = dtreplies;
						get_replies.apply(this);
					}
				});
			}
		},
		components : {
			'nv-header' : require('../components/header.vue')
		}
	}
</script>
<style lang="sass">
	.article {
		overflow: hidden;
		margin: 10px 5px 0px;
		> div {
			margin-top:10px;
			background: #fff;
			border-radius: 7px;
		}
		.articlebox {
			padding:10px;
			.articletitle {
				h2 {
					font-size: 20px;
					line-height: 1.3em;
				}
				> div {
					span {
						display: inlin-block;
						margin-right:10px;
						font-size: 12px;
						color: #838383;
					}
				}
			}
			.articlecontent {
				margin-top:10px;
				font-size: 16px;
  				line-height: 1.4em;
  				p{
  					margin: 0px 0px 1em 0px;
  				}
				img {
					width: 20rem;
					margin: 0px auto;
					display: block;
				}
			}
		}
		.articlereplies {
			width: 100%;
			margin-bottom: 20px;
			> div {
				font-size: 16px;
				width: 100%;
				padding: 10px;
			}
			li {
				padding: 10px;
				border-top: 1px solid #f0f0f0;
				.author_content {
					position: relative;
					img, span {
						float:left;
						display:inline-block;
					}
					span {
						margin-left: 10px;
						font-size: 14px;
						line-height: 1.4em;
					}
					img {
						width: 30px;
						height: 30px;
					}
					.re-time {
						color: #08c;
						i{
							color:#ccc;
						}
					}
				}
				.repliescon {
					margin-top: 10px;
					font-size: 14px;
					line-height: 1.4em;
					.repliescontent {
						background: #f0f0f0;
						padding: 5px 8px;
						border-radius: 5px;
						p{
							font-size: 16px;
						}
						img{
							max-width: 98%;
						}
					}
				}
			}
		}

	}
</style>