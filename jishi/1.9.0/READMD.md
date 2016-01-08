# 數據庫詳細信息

## 2015-12-17
* 修改技師用戶表之排名百分比 
alter table hh_techuser modify column percent float(5, 2);

* 添加積分日誌表
CREATE TABLE hh_score_log (
	id        INT(10)  UNSIGNED NOT NULL AUTO_INCREMENT,
	uid       INT(11)  DEFAULT '0' COMMENT '用戶ID',
	createdat DATETIME             COMMENT '添加時間',
	scoretype INT(11)  DEFAULT '0' COMMENT '積分類型',
	score     INT(11)  DEFAULT '0' COMMENT '添加積分',
	apicode   INT(11)  DEFAULT '0' COMMENT 'API編碼',
	oldscore  INT(11)  DEFAULT '0' COMMENT '加前積分',
	PRIMARY KEY(id)
);
