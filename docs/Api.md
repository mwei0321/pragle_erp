# ERP接口文档

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

| 参数名     | 类型    | 必需 | 值    | 说明                  |
| ---------- | ------- | ---- | ----- | --------------------- |
| year       | string  | 是   | 2021  | 年                    |
| keyword    | string  | 否   | name  | 名称搜索              |
| department | array   | 否   | [1,2] | 部门ID                |
| action     | string  | 否   | 1,2   | 动作,多个动作逗号隔开 |
| cycle      | integer | 否   | 1     | 周期                  |
| page       | integer | 是   | 1     | 页数                  |
| page_size  | integer | 否   | 10    | 每页条数              |

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

| 参数名     | 类型   | 必需 | 值   | 说明                              |
| ---------- | ------ | ---- | ---- | --------------------------------- |
| year       | string | 是   | 2022 | 年                                |
| department | array  | 是   | 1    | 部门                              |
| cycle      | int    | 是   | 2    | 周期(1.每天 2.每月 3.每周 4.每年) |
| name       | string | 是   | 1    | 名称                              |
| action     | array  | 是   | []   | 动作列表                          |

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

| 参数名 | 类型   | 必需 | 值    | 说明                              |
| :----- | ------ | ---- | ----- | --------------------------------- |
| year   | string | 是   | 2022  | 年                                |
| staff  | array  | 是   | [1,2] | 员工ID数组                        |
| cycle  | int    | 是   | 2     | 周期(1.每天 2.每月 3.每周 4.每年) |
| action | int    | 是   | []    | 动作列表                          |
| id     | int    | 是   | 1     | 动作id                            |
| value  | int    | 是   | 1     | 动作值                            |
| name   | string | 是   | name  | 名称                              |

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

#### 1. 团队 / 个人销售KPI条形图排行

请求地址：`erpapi/score/marketingbarchat`

请求方式：`get`

请求参数：

| 参数名   | 类型   | 必需 | 值   | 说明                 |
| -------- | ------ | ---- | ---- | -------------------- |
| type     | int    | 是   | 1    | 类型 (1.团队 2.个人) |
| year     | string | 是   | 2022 | 年份                 |
| staff_id | int    | 否   | 1    | type=2时,必传        |

返回参数：

团队

```json
{
    "code": 200,
    "msg": "return success!",
    "data": [
        {
            "name": "All Dept.", // 部门名称
            "department_id": "40232", // 部门ID
            "target": "48.00", // 部门目标
            "completed": "0.00" // 部门完成
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
            "month": "1", // 月份
            "target": "12.00", // 目标
            "completed": "0.00" // 完成
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

请求地址：`/erpapi/kpi/updepartmentaction`

请求方式：`post`

请求参数：

| 参数名        | 类型 | 必需 | 值   | 说明                   |
| ------------- | ---- | ---- | ---- | ---------------------- |
| type          | int  | 是   | 1    | 类型 （1.个人 2.团队） |
| user_id       | int  | 是   | 1    | 跟进人                 |
| action_id     | int  | 是   | 2    | 动作id                 |
| enterprise_id | int  | 是   | 1    | 企业id                 |
| department_id | int  | 否   | 0    | 部门 type=2时,否=0     |
| content       | text | 否   |      | 跟进内容               |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

####



### 客户&营销页面接口说明

#### 1.客户数量（按阶段）

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

#### 2.**客户数量(按员工)**

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

#### 3. **邮件数量(按状态)**

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