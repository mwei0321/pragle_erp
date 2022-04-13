build_runtime:
	docker build -t pragle-erp-runtime -f runtime.Dockerfile .
build_runtime_amd64:
	docker build --platform linux/amd64 -t pragle-erp-runtime -f runtime.Dockerfile .
build_amd64:
	docker build --platform linux/amd64 -t registry.cn-hongkong.aliyuncs.com/domedeahk/pragle_erp_api:latest .
build:
	docker build -t registry.cn-hongkong.aliyuncs.com/domedeahk/pragle_erp_api:latest .
push:
	docker push registry.cn-hongkong.aliyuncs.com/domedeahk/pragle_erp_api:latest
