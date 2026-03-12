<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('corporate_tenancy_application_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');

            $rawColumns = [
                ['datetime', 'import_date', '取り込み日時'],
                ['int', 'application_id', '申込ID'],
                ['varchar', 'shop_name', '店舗名'],
                ['varchar', 'property_name', '物件名'],
                ['varchar', 'room_number', '部屋番号'],
                ['varchar', 'room_key', '部屋KEY'],
                ['varchar', 'building_key', '建物KEY'],
                ['int', 'itanji_account_id', 'イタンジアカウントID'],
                ['int', 'management_company_id', '管理会社ID'],
                ['int', 'room_id', '部屋ID'],
                ['int', 'application_status_id', '申込ステータスID'],
                ['varchar', 'confirmation_status', '確認ステータス'],
                ['int', 'broker_itanji_account_id', '仲介イタンジアカウントID'],
                ['varchar', 'broker_company_name', '仲介会社名'],
                ['varchar', 'broker_company_name_kana', '仲介会社名カナ'],
                ['varchar', 'broker_mobile_phone', '仲介携帯電話番号'],
                ['varchar', 'broker_staff_name', '仲介担当者名'],
                ['varchar', 'broker_email', '仲介会社メールアドレス'],
                ['varchar', 'broker_fax', '仲介FAX番号'],
                ['varchar', 'broker_phone', '仲介固定電話番号'],
                ['varchar', 'broker_zip', '仲介会社郵便番号'],
                ['varchar', 'broker_address', '仲介会社住所'],
                ['int', 'guarantor_company_plan_id', '保証会社プランID'],
                ['varchar', 'corporation_flag', '法人フラグ'],
                ['varchar', 'paper_input_flag', '紙入力フラグ'],
                ['varchar', 'proxy_application_flag', '代理申込フラグ'],
                ['varchar', 'guarantor_reexamination_flag', '保証会社再審査フラグ'],
                ['varchar', 'applicant_editable_flag', '申込者編集可フラグ'],
                ['int', 'staff_id', '担当者ID'],
                ['varchar', 'guarantor_auto_link_flag', '保証会社自動連携フラグ'],
                ['varchar', 'contract_number', '契約番号'],
                ['varchar', 'application_created_at', '申込作成日時'],
                ['varchar', 'application_updated_at', '申込更新日時'],
                ['varchar', 'guarantor_company_name', '保証会社名'],
                ['varchar', 'guarantor_plan_name', '保証会社プラン名'],
                ['varchar', 'screening_result', '審査結果'],
                ['varchar', 'guarantee_number', '保証番号'],
                ['varchar', 'guarantee_target_amount', '保証対象額'],
                ['varchar', 'initial_guarantee_fee', '初回保証料'],
                ['varchar', 'guarantee_order', '番手'],
                ['varchar', 'applicant_email', '申込者Emailアドレス'],
                ['varchar', 'applicant_first_name', '申込者名前'],
                ['varchar', 'applicant_last_name', '申込者名字'],
                ['varchar', 'applicant_full_name', '申込者氏名'],
                ['varchar', 'property_usage_type', '賃貸物件内容使用用途'],
                ['varchar', 'property_name_detail', '賃貸物件内容物件名'],
                ['varchar', 'property_name_kana', '賃貸物件内容物件名カナ'],
                ['varchar', 'property_room_number', '賃貸物件内容部屋番号'],
                ['varchar', 'property_address', '賃貸物件内容物件住所'],
                ['varchar', 'rent', '賃貸物件内容家賃'],
                ['varchar', 'management_fee', '賃貸物件内容管理費／共益費'],
                ['varchar', 'utilities_fee', '賃貸物件内容水道光熱費'],
                ['varchar', 'neighborhood_fee', '賃貸物件内容町内会費（区費）'],
                ['varchar', 'management_transfer_fee', 'その他・管理会社振替手数料（管理会社）'],
                ['varchar', 'parking_fee', '賃貸物件内容駐車場料金'],
                ['varchar', 'other_fixed_fee', '賃貸物件内容その他固定費'],
                ['varchar', 'total_monthly_payment', '賃貸物件内容月額支払総額'],
                ['varchar', 'deposit', '賃貸物件内容敷金'],
                ['varchar', 'security_deposit', '賃貸物件内容保証金'],
                ['varchar', 'planned_move_in_date', '賃貸物件内容入居予定日'],
                ['varchar', 'desired_move_in_date', '賃貸物件内容入居希望日'],
                ['varchar', 'desired_contract_date', '賃貸物件内容契約希望日'],
                ['varchar', 'initial_payment_due_date', '賃貸物件内容初期費用入金予定日'],
                ['varchar', 'tokio_marine_insurance_flag', 'その他・保証会社東京海上ミレア少額短期保険株式会社/東京海上ウエスト少額短期保険株式会社(直接連携)'],
                ['varchar', 'corporate_name', '法人の申込者会社名'],
                ['varchar', 'corporate_name_kana', '法人の申込者会社名（カナ）'],
                ['varchar', 'corporate_head_office_postal_code', '法人の会社情報本社所在地（郵便番号）'],
                ['varchar', 'corporate_head_office_prefecture', '法人の会社情報本社所在地（都道府県）'],
                ['varchar', 'corporate_head_office_city', '法人の会社情報本社所在地（市区町村）'],
                ['varchar', 'corporate_head_office_address', '法人の会社情報本社所在地（番地・丁目）'],
                ['varchar', 'corporate_head_office_building', '法人の会社情報本社所在地（建物名・部屋番号）'],
                ['varchar', 'corporate_head_office_phone', '法人の会社情報本社電話番号'],
                ['varchar', 'corporate_head_office_fax', '法人の会社情報本社FAX番号'],
                ['varchar', 'corporate_email', '法人の申込者メールアドレス'],
                ['varchar', 'corporate_industry', '法人の会社情報業種'],
                ['varchar', 'corporate_capital', '法人の会社情報資本金'],
                ['varchar', 'corporate_employee_count', '法人の会社情報従業員数'],
                ['varchar', 'corporate_established_date', '法人の会社情報設立年月日'],
                ['varchar', 'corporate_representative_last_name', '法人の会社代表者氏名（名字）'],
                ['varchar', 'corporate_representative_first_name', '法人の会社代表者氏名（名前）'],
                ['varchar', 'corporate_representative_last_name_kana', '法人の会社代表者氏名（名字カナ）'],
                ['varchar', 'corporate_representative_first_name_kana', '法人の会社代表者氏名（名前カナ）'],
                ['varchar', 'corporate_representative_mobile_phone', '法人の会社代表者携帯電話番号'],
                ['varchar', 'corporate_representative_postal_code', '法人の会社代表者現住所（郵便番号）'],
                ['varchar', 'corporate_representative_prefecture', '法人の会社代表者現住所（都道府県）'],
                ['varchar', 'corporate_representative_city', '法人の会社代表者現住所（市区町村）'],
                ['varchar', 'corporate_representative_address', '法人の会社代表者現住所（番地・丁目）'],
                ['varchar', 'corporate_representative_building', '法人の会社代表者現住所（建物名・部屋番号）'],
                ['varchar', 'corporate_contact_last_name', '法人の申込者担当者名（名字）'],
                ['varchar', 'corporate_contact_first_name', '法人の申込者担当者名（名前）'],
                ['varchar', 'corporate_contact_last_name_kana', '法人の申込者担当者名（名字カナ）'],
                ['varchar', 'corporate_contact_first_name_kana', '法人の申込者担当者名（名前カナ）'],
                ['varchar', 'corporate_contact_department', '法人の申込者担当者所属部署'],
                ['varchar', 'corporate_contact_phone', '法人の申込者担当者電話番号'],
                ['varchar', 'occupant1_last_name', '法人の入居者1氏名（名字）'],
                ['varchar', 'occupant1_first_name', '法人の入居者1氏名（名前）'],
                ['varchar', 'occupant1_last_name_kana', '法人の入居者1氏名（名字カナ）'],
                ['varchar', 'occupant1_first_name_kana', '法人の入居者1氏名（名前カナ）'],
                ['varchar', 'occupant1_relationship', '法人の入居者1続柄'],
                ['varchar', 'occupant1_birth_date', '法人の入居者1生年月日'],
                ['varchar', 'occupant1_age', '法人の入居者1年齢'],
                ['varchar', 'occupant1_mobile_phone', '法人の入居者1携帯電話番号'],
                ['varchar', 'occupant1_annual_income', '法人の入居者1税込年収'],
                ['varchar', 'occupant2_last_name', '法人の入居者2氏名（名字）'],
                ['varchar', 'occupant2_first_name', '法人の入居者2氏名（名前）'],
                ['varchar', 'occupant2_last_name_kana', '法人の入居者2氏名（名字カナ）'],
                ['varchar', 'occupant2_first_name_kana', '法人の入居者2氏名（名前カナ）'],
                ['varchar', 'occupant2_relationship', '法人の入居者2続柄'],
                ['varchar', 'occupant2_birth_date', '法人の入居者2生年月日'],
                ['varchar', 'occupant2_age', '法人の入居者2年齢'],
                ['varchar', 'occupant2_mobile_phone', '法人の入居者2携帯電話番号'],
                ['varchar', 'occupant2_annual_income', '法人の入居者2税込年収'],
                ['varchar', 'occupant3_last_name', '法人の入居者3氏名（名字）'],
                ['varchar', 'occupant3_first_name', '法人の入居者3氏名（名前）'],
                ['varchar', 'occupant3_last_name_kana', '法人の入居者3氏名（名字カナ）'],
                ['varchar', 'occupant3_first_name_kana', '法人の入居者3氏名（名前カナ）'],
                ['varchar', 'occupant3_relationship', '法人の入居者3続柄'],
                ['varchar', 'occupant3_birth_date', '法人の入居者3生年月日'],
                ['varchar', 'occupant3_age', '法人の入居者3年齢'],
                ['varchar', 'occupant3_mobile_phone', '法人の入居者3携帯電話番号'],
                ['varchar', 'occupant3_annual_income', '法人の入居者3税込年収'],
                ['varchar', 'occupant4_last_name', '法人の入居者4氏名（名字）'],
                ['varchar', 'occupant4_first_name', '法人の入居者4氏名（名前）'],
                ['varchar', 'occupant4_last_name_kana', '法人の入居者4氏名（名字カナ）'],
                ['varchar', 'occupant4_first_name_kana', '法人の入居者4氏名（名前カナ）'],
                ['varchar', 'occupant4_relationship', '法人の入居者4続柄'],
                ['varchar', 'occupant4_birth_date', '法人の入居者4生年月日'],
                ['varchar', 'occupant4_age', '法人の入居者4年齢'],
                ['varchar', 'occupant4_mobile_phone', '法人の入居者4携帯電話番号'],
                ['varchar', 'occupant4_annual_income', '法人の入居者4税込年収'],
                ['varchar', 'emergency_contact_last_name', '法人の緊急連絡先氏名（名字）'],
                ['varchar', 'emergency_contact_first_name', '法人の緊急連絡先氏名（名前）'],
                ['varchar', 'emergency_contact_last_name_kana', '法人の緊急連絡先氏名（名字カナ）'],
                ['varchar', 'emergency_contact_first_name_kana', '法人の緊急連絡先氏名（名前カナ）'],
                ['varchar', 'emergency_contact_gender', '法人の緊急連絡先性別'],
                ['varchar', 'emergency_contact_birth_date', '法人の緊急連絡先生年月日'],
                ['varchar', 'emergency_contact_age', '法人の緊急連絡先年齢'],
                ['varchar', 'emergency_contact_relationship', '法人の緊急連絡先続柄'],
                ['varchar', 'emergency_contact_mobile_phone', '法人の緊急連絡先携帯電話番号'],
                ['varchar', 'emergency_contact_home_phone', '法人の緊急連絡先自宅電話番号'],
                ['varchar', 'emergency_contact_zip', '法人の緊急連絡先自宅住所（郵便番号）'],
                ['varchar', 'emergency_contact_prefecture', '法人の緊急連絡先自宅住所（都道府県）'],
                ['varchar', 'emergency_contact_city', '法人の緊急連絡先自宅住所（市区町村）'],
                ['varchar', 'emergency_contact_address', '法人の緊急連絡先自宅住所（番地・丁目）'],
                ['varchar', 'emergency_contact_building', '法人の緊急連絡先自宅住所（建物名・部屋番号）'],
                ['varchar', 'emergency_contact_company_name', '法人の緊急連絡先勤務先名'],
                ['varchar', 'emergency_contact_company_name_kana', '法人の緊急連絡先勤務先名（カナ）'],
                ['varchar', 'emergency_contact_work_phone', '法人の緊急連絡先勤務先電話番号'],
                ['varchar', 'guarantor_last_name', '法人の連帯保証人氏名（名字）'],
                ['varchar', 'guarantor_first_name', '法人の連帯保証人氏名（名前）'],
                ['varchar', 'guarantor_last_name_kana', '法人の連帯保証人氏名（名字カナ）'],
                ['varchar', 'guarantor_first_name_kana', '法人の連帯保証人氏名（名前カナ）'],
                ['varchar', 'guarantor_gender', '法人の連帯保証人性別'],
                ['varchar', 'guarantor_relationship', '法人の連帯保証人続柄'],
                ['varchar', 'guarantor_birth_date', '法人の連帯保証人生年月日'],
                ['varchar', 'guarantor_age', '法人の連帯保証人年齢'],
                ['varchar', 'guarantor_mobile_phone', '法人の連帯保証人携帯電話番号'],
                ['varchar', 'guarantor_home_phone', '法人の連帯保証人自宅電話番号'],
                ['varchar', 'guarantor_zip', '法人の連帯保証人現住所（郵便番号）'],
                ['varchar', 'guarantor_prefecture', '法人の連帯保証人現住所（都道府県）'],
                ['varchar', 'guarantor_city', '法人の連帯保証人現住所（市区町村）'],
                ['varchar', 'guarantor_address', '法人の連帯保証人現住所（番地・丁目）'],
                ['varchar', 'guarantor_building', '法人の連帯保証人現住所（建物名・部屋番号）'],
                ['varchar', 'guarantor_residence_type', '法人の連帯保証人住居種別'],
                ['varchar', 'guarantor_job', '法人の連帯保証人お勤め先職業'],
                ['varchar', 'guarantor_workplace_name', '法人の連帯保証人お勤め先勤務先/学校名'],
                ['varchar', 'guarantor_workplace_name_kana', '法人の連帯保証人お勤め先勤務先/学校名（カナ）'],
                ['varchar', 'guarantor_workplace_phone', '法人の連帯保証人お勤め先勤務先電話番号'],
                ['varchar', 'guarantor_workplace_zip', '法人の連帯保証人お勤め先勤務先所在地（郵便番号）'],
                ['varchar', 'guarantor_workplace_prefecture', '法人の連帯保証人お勤め先勤務先所在地（都道府県）'],
                ['varchar', 'guarantor_workplace_city', '法人の連帯保証人お勤め先勤務先所在地（市区町村）'],
                ['varchar', 'guarantor_workplace_address', '法人の連帯保証人お勤め先勤務先所在地（番地・丁目）'],
                ['varchar', 'guarantor_workplace_building', '法人の連帯保証人お勤め先勤務先所在地（建物名・部屋番号）'],
                ['varchar', 'guarantor_workplace_industry', '法人の連帯保証人お勤め先業種'],
                ['varchar', 'guarantor_workplace_capital', '法人の連帯保証人お勤め先資本金'],
                ['varchar', 'guarantor_workplace_employee_count', '法人の連帯保証人お勤め先従業員数'],
                ['varchar', 'guarantor_workplace_established_date', '法人の連帯保証人お勤め先設立年月日'],
                ['varchar', 'guarantor_annual_income', '法人の連帯保証人お勤め先税込年収'],
                ['varchar', 'guarantor_years_employed', '法人の連帯保証人お勤め先勤続年数'],
                ['varchar', 'corporate_company_registry_document', '法人の会社情報会社謄本'],
                ['int', 'created_user_id', 'データ登録スタッフID'],
                ['datetime', 'user_created_at', 'データ登録日時（ スタッフ）'],
                ['int', 'updated_user_id', 'データ更新スタッフID'],
                ['datetime', 'user_updated_at', 'データ更新日時（スタッフ）'],
                ['int', 'deleted_user_id', 'データ削除スタッフID'],
                ['datetime', 'user_deleted_at', 'データ削除日時（スタッフ）'],
            ];

            $columns = [];
            foreach ($rawColumns as [$type, $name, $comment]) {
                if (! isset($columns[$name])) {
                    $columns[$name] = [$type, $comment];
                    continue;
                }

                $columns[$name][1] .= ' / ' . $comment;
            }

            foreach ($columns as $name => [$type, $comment]) {
                switch ($type) {
                    case 'int':
                        $table->integer($name)->nullable()->comment($comment);
                        break;
                    case 'datetime':
                        $table->dateTime($name)->nullable()->comment($comment);
                        break;
                    case 'varchar':
                        // 列数が非常に多く row size 制限に達するため、varchar 指定項目は text で保持する
                        $table->text($name)->nullable()->comment($comment);
                        break;
                    default:
                        throw new InvalidArgumentException("Unsupported column type: {$type}");
                }
            }

            $table->dateTime('created_at')->nullable()->comment('データ登録日時');
            $table->dateTime('updated_at')->nullable()->comment('データ更新日時');
            $table->dateTime('deleted_at')->nullable()->comment('データ削除日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_tenancy_application_logs');
    }
};
