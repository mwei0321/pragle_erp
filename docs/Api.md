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

| 参数名        | 类型    | 必需 | 值   | 说明   |
| ------------- | ------- | ---- | ---- | ------ |
| department_id | integer | 是   | 1    | 部门ID |
| year          | string  | 是   | 2021 | 年     |

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

说明：因该接口数据较多，参数以`json`数据格式提交

请求参数：

| 参数名   | 类型    | 必需 | 值   | 说明                     |
| -------- | ------- | ---- | ---- | ------------------------ |
| staff_id | integer | 否   | 1    | 员工kpi,不传默认当前用户 |
| year     | string  | 是   | 2021 | 年                       |

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

地址：`/erpapi/kpi/departmentartion`

方式：`get`

说明：因该接口数据较多，参数以`json`数据格式提交

请求参数：

| 参数名        | 类型    | 必需 | 值   | 说明   |
| ------------- | ------- | ---- | ---- | ------ |
| department_id | integer | 是   | 1    | 部门ID |
| year          | string  | 是   | 2021 | 年     |

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
            "year": "2021" # 年份
        }
    ],
    "page": []
}
```



#### 5. 部门员工 KPI 写入更新

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
	"year": 2020,
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

#### 6. 企业部门动作KPI列表

请求地址：`/erpapi/kpi/updepartmentaction`

请求方式：`post`

请求参数：

| 参数名        | 类型 | 必需 | 值   | 说明                                         |
| ------------- | ---- | ---- | ---- | -------------------------------------------- |
| department_id | int  | 是   | 1    | 企业ID                                       |
| cycle         | int  | 是   | 2    | 周期(1.每天 2.每月 3.每周 4.每年)            |
| staff_id      | int  | 是   | 1    | 员工ID                                       |
| action_id     | int  | 是   | 1    | 动作ID                                       |
| action_type   | int  | 是   | 1    | 动作类型 (1.数字字符 2.单选)                 |
| action_value  | int  | 是   | 1    | 动作值: 1.数字字符 2.单选为(1.选中 0.未选中) |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

#### 7. 企业员工动作KPI列表

请求地址：`/erp/department/upstaffaction`

请求方式：`post`

请求参数：

| 参数名        | 类型 | 必需 | 值   | 说明                                         |
| ------------- | ---- | ---- | ---- | -------------------------------------------- |
| department_id | int  | 是   | 1    | 企业ID                                       |
| cycle         | int  | 是   | 2    | 周期(1.每天 2.每月 3.每周 4.每年)            |
| staff_id      | int  | 是   | 1    | 员工ID                                       |
| action_id     | int  | 是   | 1    | 动作ID                                       |
| action_type   | int  | 是   | 1    | 动作类型 (1.数字字符 2.单选)                 |
| action_value  | int  | 是   | 1    | 动作值: 1.数字字符 2.单选为(1.选中 0.未选中) |

返回参数：

```json
{
    "code": 200,
    "message": "ok",
    "data": []
}
```

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