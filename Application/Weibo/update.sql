ALTER TABLE  `ocenter_weibo_topic` ADD  `weibo_num` INT( 11 ) NOT NULL DEFAULT '0' COMMENT  '微博数';
ALTER TABLE  `ocenter_weibo_topic` ADD  `status` TINYINT NOT NULL DEFAULT  '1';

DROP TABLE IF EXISTS `ocenter_weibo_topic_link`;
CREATE TABLE IF NOT EXISTS `ocenter_weibo_topic_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weibo_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='话题、微博关联表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ocenter_weibo_cache`;
CREATE TABLE IF NOT EXISTS `ocenter_weibo_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weibo_id` int(11) NOT NULL,
  `groups` varchar(100) NOT NULL,
  `cache_html` text NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='微博html缓存表' AUTO_INCREMENT=1 ;