# 1ERP接口文档

### 接口说明

+ 以`http`或`https`协议请求服务端
+ 请求头需带`Authorization`以`Bearer`类型请求

### 请求地址

+ 正式：https://industryapi.domedea.com

### 公用接口

#### 1. 获取企业部门

请求地址：`/erp/enterprise/department`

请求方式：`get`

请求参数：

| 参数名        | 类型 | 必需 | 值   | 说明   |
| ------------- | ---- | ---- | ---- | ------ |
| enterprise_id | int  | 否   | 1    | 企业ID |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": [
        {
            "id": 39,
            "enterprise_id": 91796,
        }
    ]
}
```

#### 2. 部门员工

请求地址：`/erp/department/staff`

请求方式：`get`

请求参数：

| 参数名        | 类型 | 必需 | 值   | 说明   |
| ------------- | ---- | ---- | ---- | ------ |
| department_id | int  | 是   | 1    | 部门ID |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": [
        {
            "id": 39,
            "enterprise_id": 91796,
        }
    ]
}
```

### 企业KPI相关接口

#### 1.部门&员工KPI列表

地址：`/erpapi/kpi/getmarketingkpi`

方式：`get`

请求参数：

| 参数名        | 类型   | 必需 | 值    | 说明   |
| ------------- | ------ | ---- | ----- | ------ |
| department_id | string | 是   | 1,2,3 | 部门ID |
| year          | string | 是   | 2021  | 年     |

返回参数：

```json
{
	"code": 200,
	"msg": "return success!",
	"data": {
		"year": 2020,
		"department_id": 1,
		"group_kpi": [{
			"group_id": 1,
			"group_name": "name",
			"kpi_value": [1,2,3,4,5,5,6,7,7,8,9,9]
		}],
		"staff_id": [{
			"staff_id": 1,
			"staff_name": "name",
			"kpi_value": [1,2,3,4,5,5,6,7,7,8,9,9]
		}]
	},
	"page": []
}
```

#### 2. Kpi记录年列表

地址：`/erpapi/kpi/year`

方式：`get`

请求参数：无

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": [
        "2022"
    ],
    "page": []
}
```



#### 3. 企业员工动作KPI列表

地址：`/erpapi/kpi/staffaction`

方式：`get`

请求参数：

| 参数名    | 类型    | 必需 | 值   | 说明                     |
| --------- | ------- | ---- | ---- | ------------------------ |
| year      | string  | 是   | 2021 | 年                       |
| staff_id  | integer | 否   | 1    | 员工kpi,不传默认当前用户 |
| keyword   | string  | 否   | name | 名称搜索                 |
| action    | string  | 否   | 1,2  | 动作,多个动作逗号隔开    |
| cycle     | integer | 否   | 1    | 周期                     |
| page      | integer | 是   | 1    | 页数                     |
| page_size | integer | 否   | 10   | 每页条数                 |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "id": "12", # ID
            "cycle": "12", # 周期(1.每天 2.每月 3.每周 4.每年)
            "action_id": "12", # 动作ID
            "action_value": "1", # 动作值
            "action_type": "1", # 动作类型 (1.数字字符 2.单选)
            "staff_id": "1", # 员工ID
            "year": "2021" # 年份
        }
    ],
    "page": []
}
```



#### 4. 企业部门动作KPI列表

地址：`/erpapi/kpi/departmentaction`

方式：`get`

说明：因该接口数据较多，参数以`json`数据格式提交

请求参数：

| 参数名        | 类型    | 必需 | 值    | 说明                  |
| ------------- | ------- | ---- | ----- | --------------------- |
| enterprise_id | int     | 是   | 1     | 企业ID                |
| year          | string  | 是   | 2021  | 年                    |
| keyword       | string  | 否   | name  | 名称搜索              |
| department    | array   | 否   | [1,2] | 部门ID                |
| action        | string  | 否   | 1,2   | 动作,多个动作逗号隔开 |
| cycle         | integer | 否   | 1     | 周期                  |
| page          | integer | 是   | 1     | 页数                  |
| page_size     | integer | 否   | 10    | 每页条数              |

返回参数：

```json
{43
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "id": "12", # ID
            "cycle": "12", # 周期(1.每天 2.每月 3.每周 4.每年)
            "action_id": "12", # 动作ID
            "action_value": "1", # 动作值
            "action_type": "1", # 动作类型 (1.数字字符 2.单选)
            "year": "2021" # 年份
        }
    ],
    "page": []
}

[{'cycle':12,'action_id':1,'department_id':1},{'cycle':12,'action_id':1,'department_id':1}]


```



#### 5. 部门员工销售 KPI 写入更新

地址：`/erpapi/kpi/upmarketingkpi`

方式：`post`

说明：因该接口数据较多，参数以`json`数据格式提交

请求参数：

| 参数名        | 类型    | 必需 | 值   | 说明                       |
| ------------- | ------- | ---- | ---- | -------------------------- |
| department_id | integer | 是   | 1    | 部门ID                     |
| staff_id      | integer | 是   | 1    | 员工ID                     |
| month         | integer | 是   | 1    | 月份                       |
| year          | string  | 是   | 2021 | 年                         |
| target        | float   | 是   | 1    | 目标金额                   |
| group_id      | integer | 是   | 1    | 部门分组ID                 |
| id            | integer | 否   | 0    | 如果有值为更新，否则为写入 |
| enterprise_id | int     | 是   | 1    | 企业id                     |

请求参数示例：

```json
{
	"year": 2022,
	"department_id": 1,
	"group_kpi": {
		"2": [1, 2, 3, 4, 5, 5, 6, 7, 7, 8, 9, 9],
		"5": [1, 2, 3, 4, 5, 5, 6, 7, 7, 8, 9, 9]
	},
	"staff_kpi": {
		"2": [1, 2, 3, 4, 5, 5, 6, 7, 7, 8, 9, 9],
		"5": [1, 2, 3, 4, 5, 5, 6, 7, 7, 8, 9, 9]
	}
}
```



返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": [
        {
            "id": 39,
            "enterprise_id": 91796,
        }
    ]
}
```

#### 6. 企业部门动作KPI写入

请求地址：`/erpapi/kpi/updepartmentaction`

请求方式：`post`

请求参数：

| 参数名        | 类型   | 必需 | 值   | 说明                              |
| ------------- | ------ | ---- | ---- | --------------------------------- |
| year          | string | 是   | 2022 | 年                                |
| department    | array  | 是   | 1    | 部门                              |
| cycle         | int    | 是   | 2    | 周期(1.每天 2.每月 3.每周 4.每年) |
| name          | string | 是   | 1    | 名称                              |
| action        | array  | 是   | []   | 动作列表                          |
| enterprise_id | int    | 是   | 1    | 企业id                            |

```json
{
    "name":"name",
    "cycle":"1",
    "department":[1,2],
    "action":[1,2]
}
```



返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 7. 企业员工动作KPI写入

请求地址：`/erpapi/kpi/upstaffaction`

请求方式：`post`

请求参数：

| 参数名        | 类型   | 必需 | 值    | 说明                              |
| :------------ | ------ | ---- | ----- | --------------------------------- |
| year          | string | 是   | 2022  | 年                                |
| staff         | array  | 是   | [1,2] | 员工ID数组                        |
| cycle         | int    | 是   | 2     | 周期(1.每天 2.每月 3.每周 4.每年) |
| action        | int    | 是   | []    | 动作列表                          |
| id            | int    | 是   | 1     | 动作id                            |
| value         | int    | 是   | 1     | 动作值                            |
| name          | string | 是   | name  | 名称                              |
| enterprise_id | int    | 是   | 1     | 企业id                            |

```json
{
    "name":"name",
    "cycle":"1",
    "staff":[1,2],
    "action":[
        {"id":1,"value":2},
        {"id":1,"value":2},
    ]
}
```



返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 8. 动作项KPI列表

请求地址：`/erpapi/kpi/getkpiactionlist`

请求方式：`get`

请求参数：无

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 9. 删除-组-销售KPI

请求地址：`erpapi/kpi/delgroupmarketing`

请求方式：`get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 是   | 1    | kpi id |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 10. 删除-员工-销售KPI

请求地址：`erpapi/kpi/delstaffmarkting`

请求方式：`get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 是   | 1    | kpi id |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 11. 删除-员工-动作KPI

请求地址：`erpapi/kpi/delstaffaction`

请求方式：`get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 是   | 1    | kpi id |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 12. 删除-部门-动作KPI

请求地址：`erpapi/kpi/deldepartmentaction`

请求方式：`get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 是   | 1    | kpi id |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

### 图表排行

#### 1. 团队 / 个人积分条形图排行

请求地址：`erpapi/score/getbarchat`

请求方式：`get`

请求参数：

| 参数名        | 类型   | 必需 | 值   | 说明                 |
| ------------- | ------ | ---- | ---- | -------------------- |
| enterprise_id | int    | 是   | 1    | 企业ID               |
| type          | int    | 是   | 1    | 类型 (1.个人 2.团队) |
| year          | string | 是   | 2022 | 年份                 |
| department_id | int    | 否   | 1    | 部门                 |

返回参数：

团队

```json
{
	"code": 200,
	"msg": "return success!",
	"data": [{
		"name": "lses", // 部门名称
		"department_id": "40232", // 部门ID
		"count": ["0", "1", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"] // 12 个月的积分
	}],
	"page": []
}
```



个人

```json
{
	"code": 200,
	"msg": "return success!",
	"data": {
		{
			"name": "weili",
			"staff_id": "90483",
			"count": ["0", "5", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"]  // 12 个月的积分
		},
		{
			"name": "mala", // 员工名称
			"staff_id": "90484", // 员工ID
			"count": ["0", "3", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"] // 12 个月的积分
		}
	},
	"page": []
}
```



#### 2. 团队 / 个人销售KPI条形图排行

请求地址：`erpapi/kpi/marketingbarchat`

请求方式：`get`

请求参数：

| 参数名        | 类型   | 必需 | 值   | 说明                            |
| ------------- | ------ | ---- | ---- | ------------------------------- |
| enterprise_id | int    | 是   | 1    | 企业id                          |
| type          | int    | 是   | 1    | 类型 (1.个人 2.团队)            |
| year          | string | 是   | 2022 | 年份                            |
| department_id | int    | 否   | 1    | 部门                            |
| is_year_all   | int    | 是   | 1    | 是否是统计一年的总和(1.是 0.否) |

返回参数：

团队

```json
# is_year_all = 1
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "name": "All Dept.", // 部门名称
            "department_id": "40232", // 部门ID
            "target": 10  // 1年目标
            "completed": 10  // 1年的完成
        },
    ],
}
# is_year_all = 0
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "name": "All Dept.", // 部门名称
            "department_id": "40232", // 部门ID
            "target": ["0", "5", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"]  // 12 个月的目标
            "completed": ["0", "5", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"]  // 12 个月的完成
        },
    ],
}
```



个人

```json
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "first_name": "Andrew", // 姓
            "last_name": "Ye", // 名
            "staff_id": "90486", // 员工ID
            "target": ["0", "5", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"]  // 12 个月的目标
            "completed": ["0", "5", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"]  // 12 个月的完成
        },
    ]
}
```

###





### 动作跟进

#### 1. 动作跟进列表

地址：`/erpapi/follow/getlist`

方式：`get`

请求参数：

| 参数名        | 类型 | 必需 | 值   | 说明                   |
| ------------- | ---- | ---- | ---- | ---------------------- |
| type          | int  | 是   | 1    | 类型 （1.个人 2.团队） |
| department_id | int  | 否   | 1    | 部门                   |
| action_id     | int  | 否   | 1    | 动作列表               |
| staff_id      | int  | 否   | 1    | 员工                   |

返回参数：

```json
{
    "code": 200, 
    "msg": "return success!",
    "data": {
        "items": [
            {
                "id": "1",
                "user_id": "1",
                "enterprise_id": "1",
                "department_id": "1",
                "action_id": "1",
                "description": "sssssssssssssss",
                "type": "1",
                "follow_time": "0",
                "utime": "0",
                "ctime": "0"
            },
        ],
        "count": "2"
    },
    "page": []
}
```



#### 2. 动作跟进写入

请求地址：`/erpapi/follow/update`

请求方式：`post`

请求参数：

| 参数名         | 类型   | 必需 | 值                  | 说明                   |
| -------------- | ------ | ---- | ------------------- | ---------------------- |
| type           | int    | 是   | 1                   | 类型 （1.个人 2.团队） |
| staff_id       | int    | 是   | 1                   | 跟进人                 |
| action_id      | int    | 是   | 2                   | 动作id                 |
| enterprise_id  | int    | 是   | 1                   | 企业id                 |
| department_id  | int    | 否   | 0                   | 部门 type=2时,否=0     |
| content        | text   | 否   |                     | 跟进内容               |
| attachment_url | string | 否   |                     | 附件UR地址             |
| follow_time    | string | 是   | 2022-01-26 14:15:16 | 跟进时间               |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 3. 动作/目标年-列表

请求地址：`/erpapi/kpi/year`

请求方式：`get`

请求参数：无

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "id": "2", # ID
            "year": "2023" # 年
        },
    ],
    "page": []
}
```

#### 3. 动作/目标年-添加更新

请求地址：`/erpapi/kpi/upyear`

请求方式：`get`

请求参数：

| 参数名 | 类型   | 必需 | 值   | 说明                      |
| ------ | ------ | ---- | ---- | ------------------------- |
| year   | string | 是   | 2022 | 年份                      |
| id     | int    | 否   | 1    | ID ,有时为更新,没有为添加 |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data":[],
    "page": []
}
```

#### 3. 动作/目标年-删除

请求地址：`/erpapi/kpi/delyear`

请求方式：`get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明 |
| ------ | ---- | ---- | ---- | ---- |
| id     | int  | 否   | 1    | id   |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data":[],
    "page": []
}
```

### 订单

#### 1. 手动录入

接口：`/erpapi/order/manual`

请求参数：

| 参数名            | 类型   | 必需 | 值    | 说明         |
| ----------------- | ------ | ---- | ----- | ------------ |
| buyer_enterpriise_id | int | 是 | 1 | 购买的用户的企业ID |
| buyer_user_id | int    | 是   | 1     | 购买的用户ID |
| seller_enterpriise_id | int | 是 | 1 | 卖家的企业ID |
| seller_user_id | int    | 是   | 1     | 卖家的用户ID |
| total_amount      | int    | 是   | 111   | 订单金额     |
| product_id        | int    | 是   | 1     | 商品ID       |
| product_detail_id | int    | 是   | 1     | 商品属性id   |
| product_number    | int    | 是   | 1     | 商品数量     |
| product_price     | int    | 是   | 1     | 商品价格     |
| order_num         | string | 是   | 23123 | 订单号       |
| money_type        | string | 是   | RMB   | 货币         |
| created_at        | int    | 是   |       | 时间戳(秒)   |
| description       | string | 否   |       | 描述         |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 2. 删除订单

请求地址：`/erpapi/order/delete`

请求方式：`get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明 |
| ------ | ---- | ---- | ---- | ---- |
| id     | int  | 是   | 1    | id   |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data":[],
}
```

###



### 客户&营销页面接口说明

#### 1. 客户数量（按阶段）

接口：`/erp/enterprise/statisticschart`

请求参数：

| 参数名   | 类型   | 必需 | 值                  | 说明     |
| -------- | ------ | ---- | ------------------- | -------- |
| start_at | string | 是   | 2021-11-02 00:00:00 | 开始时间 |
| end_at   | string | 是   | 2021-11-08 23:59:59 | 结束时间 |
| user     | string | 是   | 1                   | 用户ID   |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": [
        {
            "id": 39,
            "enterprise_id": 91796,
            "user_id": 90484,
            "invalid": 17907, //无效客户数
            "target": 11090, //目标客户数
            "potential": 555, //潜在客户数
            "interested": 62, //意向客户数
            "negotiating": 0, // 商务客户数
            "deal": 0, // 成交客户数
            "day": "2021-11-06" // 日期
        }
    ]
}
```

#### 2. 客户数量(按员工)

接口：`/erp/enterprise/statisticscount`

请求参数：

| 参数名     | 类型   | 必需 | 值                  | 说明     |
| ---------- | ------ | ---- | ------------------- | -------- |
| start_at   | string | 是   | 2021-11-02 00:00:00 | 开始时间 |
| end_at     | string | 是   | 2021-11-08 23:59:59 | 结束时间 |
| state      | int    | 是   | 1                   | 阶段ID   |
| enterprise | int    | 是   | 91796               | 企业ID   |

请求参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": [
        {
            "id": 115,
            "user_id": 90484,
            "username": "Chang SoStron", // 员工名称
            "num": 62 // 客户数
        },
    ]
}
```

#### 3. 邮件数量(按状态)

请求：`/erp/enterprise/statisticsemailchart`

请求参数：

| 参数名     | 类型   | 必需 | 值                  | 说明     |
| ---------- | ------ | ---- | ------------------- | -------- |
| start_at   | string | 是   | 2021-11-02 00:00:00 | 开始时间 |
| end_at     | string | 是   | 2021-11-08 23:59:59 | 结束时间 |
| user       | int    | 是   | 1                   | 用户ID   |
| enterprise | int    | 是   | 91796               | 企业ID   |

请求参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": [
        {
            "id": 74,
            "enterprise_id": 91796,
            "user_id": 143031, 
            "send": 0, // 发送
            "receive": 0, // 接收
            "day": "2021-11-06" // 日期
        }
    ]
}
```



#### 4. 邮件数量(按用户)

请求：`/erp/enterprise/statisticsemail`

请求参数：

| 参数名     | 类型   | 必需 | 值                  | 说明     |
| ---------- | ------ | ---- | ------------------- | -------- |
| start_at   | string | 是   | 2021-11-02 00:00:00 | 开始时间 |
| end_at     | string | 是   | 2021-11-08 23:59:59 | 结束时间 |
| user       | int    | 是   | 1                   | 用户ID   |
| enterprise | int    | 是   | 91796               | 企业ID   |

请求参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": [
        {
            "username": "Yuna", // 员工名称
            "send": "0", // 发送数量
            "receive": "0" // 接收数量
        }
    ]
}
```

### 



### 统计

#### 1. 动作时间段统计

请求：`/erpapi/actionstat/timebucket`

请求参数：

| 参数名        | 类型   | 必需 | 值         | 说明     |
| ------------- | ------ | ---- | ---------- | -------- |
| enterprise_id | string | 是   | 91796      | 企业ID   |
| stime         | string | 是   | 2021-04-01 | 开始时间 |
| etime         | int    | 是   | 2021-04-10 | 结束时间 |
| action_id     | int    | 否   | 1          | 动作id   |
| department_id | int    | 否   | 1          | 部门id   |
| staff_id      | int    | 否   | 1          | 员工id   |

请求参数：

```json
{
    "code": 200,
    "msg": "response success",
    "data": [
        {
            "department_id": "37532",
            "staff_id": "144273",
            "action_id": "94",
            "value": "1"
        },
    ],
    "page": []
}
```

### 咨询

#### 1. 咨询列表

请求：`/erpapi/consult/list`

请求参数：

| 参数名    | 类型 | 必需 | 值   | 说明 |
| --------- | ---- | ---- | ---- | ---- |
| page      | int  | 否   | 1    | 页码 |
| page_size | int  | 否   | 1    | 条数 |

返回参数：

| 参数名       | 类型   | 说明     |
| ------------ | ------ | -------- |
| consult_name | string | 联系姓名 |
| consult_nike | string | 联系称呼 |
| company_name | string | 公司名称 |
| email        | string | 邮箱地址 |
| phone        | string | 联系电话 |
| ctime        | int    | 创建时间 |



```json
{
    "code": 200,
    "msg": "return success!",
    "data": {
        "items": [
            {
                "id": 1,
                "consult_name": "test",
                "consult_nike": "test1",
                "company_name": "test2",
                "email": "test@tes.com",
                "phone": "12345561234",
                "is_consult": 0,
                "consult_content": null,
                "consult_staff": 0,
                "utime": 1656515827,
                "ctime": 1656515790
            }
        ],
        "count": 1
    },
    "page": []
}
```

#### 2. 咨询添加,修改

请求：`/erpapi/consult/update`

请求参数：

| 参数名       | 类型   | 必填 | 值            | 说明               |
| ------------ | ------ | ---- | ------------- | ------------------ |
| consult_name | string | 是   | consult_name  | 联系姓名           |
| consult_nike | string | 是   | consult_nike  | 联系称呼联系称呼否 |
| company_name | string | 是   | company_name  | 公司名称           |
| email        | string | 是   | test@test.com | 邮箱地址           |
| phone        | string | 是   | 13645671234   | 联系电话           |

请求参数：

```json
{"code":200,"msg":"return success!","data":[],"page":[]}
```



### 项目跟进

#### 1. 项目跟进列表

请求：`erpapi/project/getlist`

请求方式: `Get`

请求参数：

| 参数名                 | 类型 | 必需 | 值   | 说明                                                  |
| ---------------------- | ---- | ---- | ---- | ----------------------------------------------------- |
| staff_id               | int  | 否   | 1    | 员工id                                                |
| state                  | int  | 否   | 1    | 跟进状态 ( 1成交，2丢失，3跟进中，4无回应，5项目推迟) |
| customer_enterprise_id | int  | 否   | 1    | 客户企业id搜索                                        |
| product_id             | int  | 否   | 1    | 产品id搜索                                            |
| page                   | int  | 否   | 1    | 页码                                                  |
| page_size              | int  | 否   | 1    | 条数                                                  |

返回参数：

| 参数名                 | 类型   | 说明                                           |      |
| ---------------------- | ------ | ---------------------------------------------- | ---- |
| id                     | string | id                                             |      |
| staff_id               | array | 跟进员工id                                     |      |
| enterprise_id          | string | 跟进员工企业id                                 |      |
| customer_id            | string | 客户联系人id                                   |      |
| customer_enterprise_id | string | 客户企业id                                     |      |
| name                   | string | 项目名称                                       |      |
| imgs                   | string | 项目图片                                       |      |
| country                | string | 国家                                           |      |
| province               | string | 省                                             |      |
| city                   | string | 市/区                                          |      |
| district               | string | 县                                             |      |
| start_at               | string | 项目开始时间                                   |      |
| end_at                 | string | 项目结束时间                                   |      |
| follow_times           | string | 跟进次数                                       |      |
| level                  | string | 项目级别，1重大，2重点，3重要，4普通           |      |
| state                  | string | 状态 1成交，2丢失，3跟进中，4无回应，5项目推迟 |      |
| amount                 | string | 项目金额，单位为元                             |      |
| product_id             | string | 关注产品id                                     |      |
| product_area           | string | 产品面积                                       |      |
| description            | string | 描述                                           |      |
| ctime                  | string | 创建时间                                       |      |
| utime                  | string | 更新时间                                       |      |

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {
        "items": [
            {
                "id": "2",
                "staff_id": "1",
                "enterprise_id": "1",
                "customer_id": "1",
                "customer_enterprise_id": null,
                "name": "测试项目",
                "imgs": null,
                "country": "1",
                "province": "30001",
                "city": "10001",
                "district": "40001",
                "start_at": "1577808000",
                "end_at": "1588780800",
                "follow_times": "0",
                "level": "1",
                "state": "3",
                "amount": "100.00",
                "product_id": "0",
                "product_area": "500.00",
                "description": null,
                "ctime": "1588838282",
                "utime": "0"
            }
        ],
        "count": "1"
    },
    "page": []
}
```

#### 2. 项目跟进详情

请求：`/erpapi/project/getinfo`

请求方式: `Get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 否   | 1    | 项目id |

返回参数：

| 参数名                 | 类型   | 说明                                           |      |
| ---------------------- | ------ | ---------------------------------------------- | ---- |
| id                     | string | id                                             |      |
| staff_id               | array | 跟进员工id                                     |      |
| enterprise_id          | string | 跟进员工企业id                                 |      |
| customer_id            | string | 客户联系人id                                   |      |
| customer_enterprise_id | string | 客户企业id                                     |      |
| name                   | string | 项目名称                                       |      |
| imgs                   | string | 项目图片                                       |      |
| country                | string | 国家                                           |      |
| province               | string | 省                                             |      |
| city                   | string | 市/区                                          |      |
| district               | string | 县                                             |      |
| start_at               | string | 项目开始时间                                   |      |
| end_at                 | string | 项目结束时间                                   |      |
| follow_times           | string | 跟进次数                                       |      |
| level                  | string | 项目级别，1重大，2重点，3重要，4普通           |      |
| state                  | string | 状态 1成交，2丢失，3跟进中，4无回应，5项目推迟 |      |
| amount                 | string | 项目金额，单位为元                             |      |
| product_id             | string | 关注产品id                                     |      |
| product_area           | string | 产品面积                                       |      |
| description            | string | 描述                                           |      |
| ctime                  | string | 创建时间                                       |      |
| utime                  |        |                                                |      |

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {
        "id": "2",
        "staff_id": "1",
        "enterprise_id": "1",
        "customer_id": "1",
        "customer_enterprise_id": null,
        "name": "测试项目",
        "imgs": null,
        "country": "1",
        "province": "30001",
        "city": "10001",
        "district": "40001",
        "start_at": "1577808000",
        "end_at": "1588780800",
        "follow_times": "0",
        "level": "1",
        "state": "3",
        "amount": "100.00",
        "product_id": "0",
        "product_area": "500.00",
        "description": null,
        "ctime": "1588838282",
        "utime": "0"
    },
    "page": []
}
```

#### 3. 项目跟进创建,更新

请求：`/erpapi/project/update`

请求方式: `Post`

请求参数：

| 参数名                 | 类型   | 说明                                           |      |
| ---------------------- | ------ | ---------------------------------------------- | ---- |
| id                     | int    | id 注意:有`id`为更新, 无`id`或者`0`为新增      |      |
| staff_id               | array    | 跟进员工id                                     |      |
| enterprise_id          | int    | 跟进员工企业id                                 |      |
| customer_id            | int    | 客户联系人id                                   |      |
| customer_enterprise_id | int    | 客户企业id                                     |      |
| name                   | string | 项目名称                                       |      |
| imgs                   | string | 项目图片 注意:多张有逗号隔开                   |      |
| country                | int    | 国家                                           |      |
| province               | int    | 省                                             |      |
| city                   | int    | 市/区                                          |      |
| district               | int    | 县                                             |      |
| start_at               | int    | 项目开始时间                                   |      |
| end_at                 | int    | 项目结束时间                                   |      |
| follow_times           | int    | 跟进次数                                       |      |
| level                  | int    | 项目级别，1重大，2重点，3重要，4普通           |      |
| state                  | int    | 状态 1成交，2丢失，3跟进中，4无回应，5项目推迟 |      |
| amount                 | float  | 项目金额，单位为元                             |      |
| product_id             | int    | 关注产品id                                     |      |
| product_area           | float  | 产品面积                                       |      |
| description            | string | 描述                                           |      |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {},
    "page": []
}
```

#### 4. 项目跟进删除

请求：`/erpapi/project/delete`

请求方式: `Get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 是   | 1    | 项目id |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {},
    "page": []
}
```

#### 5. 项目跟进状态更新

请求：`/erpapi/project/state`

请求方式: `Get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明                                              |
| ------ | ---- | ---- | ---- | ------------------------------------------------- |
| id     | int  | 是   | 1    | 项目id                                            |
| state  | int  | 是   | 1    | 状态值(1成交，2丢失，3跟进中，4无回应，5项目推迟) |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {},
    "page": []
}
```

### 视频

#### 1. 视频列表

请求：`erpapi/video/getlist`

请求方式: `Get`

请求参数：

| 参数名        | 类型 | 必需 | 值   | 说明                |
| ------------- | ---- | ---- | ---- | ------------------- |
| enterprise_id | int  | 是   | 1    | 企业id              |
| status        | int  | 否   | 1    | 状态 0. 禁用 1.启用 |
| group_id      | int  | 否   | 1    | 分组id              |
| language      | int  | 否   | 1    | 语言                |
| page          | int  | 否   | 1    | 页码                |
| page_size     | int  | 否   | 1    | 条数                |

返回参数：

| 参数名        | 类型   | 说明     |
| ------------- | ------ | -------- |
| id            | string | id       |
| enterprise_id | string | 企业id   |
| group_id      | string | 分组id   |
| title         | string | 标题     |
| publish_time  | string | 发布时间 |
| status        | string | 状态     |
| language      | string | 语言     |
| keyword       | string | 关键字   |
| intro         | string | 视频简介 |

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {
        "items": [
            {
                "id": "1",
                "enterprise_id": "123",
                "group_id": "12",
                "title": "这是一个视频",
                "publish_time": "2147483647",
                "status": "0",
                "language": "1",
                "keyword": "12312",
                "intro": "123123",
                "url": "12313212",
                "staff_id": "12312",
                "utime": "1675062878",
                "ctime": "1675062804"
            }
        ],
        "count": "1"
    },
    "page": []
}
```

#### 2. 视频详情

请求：`/erpapi/video/getinfo`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 是   | 1    | 项目id |

返回参数：

| 参数名        | 类型   | 说明     |
| ------------- | ------ | -------- |
| id            | string | id       |
| enterprise_id | string | 企业id   |
| group_id      | string | 分组id   |
| title         | string | 标题     |
| publish_time  | string | 发布时间 |
| status        | string | 状态     |
| language      | string | 语言     |
| keyword       | string | 关键字   |
| intro         | string | 视频简介 |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {
        "id": "1",
        "enterprise_id": "123",
        "group_id": "12",
        "title": "这是一个视频",
        "publish_time": "2147483647",
        "status": "0",
        "language": "1",
        "keyword": "12312",
        "intro": "123123",
        "url": "12313212",
        "staff_id": "12312",
        "is_del": "0",
        "utime": "1675062878",
        "ctime": "1675062804"
    },
    "page": []
}
```

#### 3. 视频创建,更新

请求：`/erpapi/video/update`

请求方式: `Post`

请求参数：

| 参数名        | 类型   | 说明                |
| ------------- | ------ | ------------------- |
| id            | string | id                  |
| enterprise_id | string | 企业id              |
| group_id      | string | 分组id              |
| title         | string | 标题                |
| publish_time  | string | 发布时间            |
| status        | string | 状态 0. 禁用 1.启用 |
| language      | string | 语言                |
| keyword       | string | 关键字              |
| intro         | string | 视频简介            |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": [],
    "page": []
}
```

#### 4. 视频删除

请求：`/erpapi/video/delete`

请求方式: `Get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明   |
| ------ | ---- | ---- | ---- | ------ |
| id     | int  | 是   | 1    | 项目id |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {},
    "page": []
}
```

#### 5. 视频状态更新

请求：`/erpapi/video/status`

请求方式: `Get`

请求参数：

| 参数名 | 类型 | 必需 | 值   | 说明                |
| ------ | ---- | ---- | ---- | ------------------- |
| id     | int  | 是   | 1    | 项目id              |
| status | int  | 是   | 1    | 状态 0. 禁用 1.启用 |

返回参数：

```json
{
    "code": 200,
    "msg": "return success!",
    "data": {},
    "page": []
}
```

### 设备，广告统计

#### 1. 设备播放统计列表

请求：`erpapi/devadv/getdev`

请求方式: `Get`

请求参数：

| 参数名    | 类型   | 必需 | 值               | 说明     |
| --------- | ------ | ---- | ---------------- | -------- |
| stime     | string | 否   | 2023-04-20       | 开始时间 |
| etime     | string | 否   | 2023-04-20       | 结束时间 |
| dev_no    | string | 否   | FE4BA59DD1D5836E | 设备号   |
| page      | int    | 否   | 1                | 页码     |
| page_size | int    | 否   | 1                | 条数     |

返回参数：

| 参数名          | 类型   | 说明            |
| --------------- | ------ | --------------- |
| id              | string | id              |
| devno           | string | 企业id          |
| dev_type        | string | 设备类型        |
| adv_id          | int    | 广告id          |
| play_num        | int    | 播放次数        |
| play_time       | int    | 播放时间 （秒） |
| enterprise_name | string | 企业名          |
| enterprise_nike | string | 企业简称        |
| device_name     | string | 设备名          |
| adv_name        | string | 广告名          |



```json
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "id": 190818,
            "devno": "FE4BA59DD1D5836E",
            "adv_id": 110180,
            "play_num": 1152,
            "play_time": 17247,
            "enterprise_name": "sgdaikin2",
            "enterprise_nike": "",
            "device_name": "WM Centrepoint",
            "dev_no": "FE4BA59DD1D5836E",
            "dev_type": "4",
            "adv_name": ""
        },
    ],
    "page": []
}
```

#### 2. 广告播放统计列表

请求：`erpapi/devadv/getadv`

请求方式: `Get`

请求参数：

| 参数名    | 类型   | 必需 | 值         | 说明     |
| --------- | ------ | ---- | ---------- | -------- |
| stime     | string | 否   | 2023-04-20 | 开始时间 |
| etime     | string | 否   | 2023-04-20 | 结束时间 |
| adv_id    | int    | 否   | 12         | 广告id   |
| page      | int    | 否   | 1          | 页码     |
| page_size | int    | 否   | 1          | 条数     |

返回参数：

| 参数名          | 类型   | 说明            |
| --------------- | ------ | --------------- |
| id              | string | id              |
| devno           | string | 企业id          |
| dev_type        | string | 设备类型        |
| adv_id          | int    | 广告id          |
| play_num        | int    | 播放次数        |
| play_time       | int    | 播放时间 （秒） |
| enterprise_name | string | 企业名          |
| enterprise_nike | string | 企业简称        |
| device_name     | string | 设备名          |
| adv_name        | string | 广告名          |



```json
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "id": 190818,
            "devno": "FE4BA59DD1D5836E",
            "adv_id": 110180,
            "play_num": 1152,
            "play_time": 17247,
            "enterprise_name": "sgdaikin2",
            "enterprise_nike": "",
            "device_name": "WM Centrepoint",
            "dev_no": "FE4BA59DD1D5836E",
            "dev_type": "4",
            "adv_name": ""
        },
    ],
    "page": []
}
```
