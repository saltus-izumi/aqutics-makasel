<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnProgressFile extends Model
{
    use RecordsUserStamps;
    use SoftDeletes;

    const FILE_KIND_REGISTRY_CERTIFICATE = 1;                  // 登記簿謄本(土地建物)
    const FILE_KIND_COST_BREAKDOWN = 2;                        // 諸費用明細書
    const FILE_KIND_RENTAL_CONTRACT = 3;                       // 賃貸借契約書（定期借家契約書）
    const FILE_KIND_IMPORTANT_EXPLANATION = 4;                 // 重要事項説明書
    const FILE_KIND_ELECTRONIC_CONTRACT_CONSENT = 5;           // 電子契約承諾証明書
    const FILE_KIND_PARENTAL_CONSENT = 6;                      // 親権者同意書
    const FILE_KIND_GUARANTOR_PLEDGE = 7;                      // 連帯保証人確約書
    const FILE_KIND_DISPUTE_PREVENTION_ORDINANCE = 8;          // 紛争防止条例
    const FILE_KIND_PRIVACY_POLICY = 9;                        // 個人情報取り扱い
    const FILE_KIND_MEMORANDUM = 10;                           // 覚書
    const FILE_KIND_SETTLEMENT_AGREEMENT = 11;                 // 示談書
    const FILE_KIND_APPLICATION_FORM = 12;                     // 入居申込書
    const FILE_KIND_RESIDENT_RECORD = 13;                      // 住民票（入居者全員）
    const FILE_KIND_IDENTITY_DOCUMENT = 14;                    // 身分証明書
    const FILE_KIND_PROFILE_PHOTO = 15;                        // 顔写真
    const FILE_KIND_SALARY_STATEMENT = 16;                     // 給与明細（収入証明）
    const FILE_KIND_PASSPORT_COPY = 17;                        // パスポート写し
    const FILE_KIND_RESIDENCE_CARD_COPY = 18;                  // 在留カード写し
    const FILE_KIND_COMPANY_REGISTRY = 19;                     // 法人全部事項証明書
    const FILE_KIND_COMPANY_SEAL_CERTIFICATE = 20;             // 法人印鑑証明書
    const FILE_KIND_FINANCIAL_STATEMENTS = 21;                 // 決算報告書（3期分）
    const FILE_KIND_TAX_CERTIFICATE = 22;                      // 納税証明書（その1、その2）
    const FILE_KIND_EMPLOYEE_CERTIFICATE = 23;                 // 従業者証明書
    const FILE_KIND_COMPANY_PROFILE = 24;                      // 会社概要
    const FILE_KIND_GUARANTOR_INCOME_PROOF = 25;               // 連帯保証人・収入証明書
    const FILE_KIND_GUARANTOR_RESIDENT_RECORD = 26;            // 連帯保証人・住民票
    const FILE_KIND_GUARANTOR_IDENTITY_DOCUMENT = 27;          // 連帯保証人・身分証明書
    const FILE_KIND_APPROVAL_NOTICE = 28;                      // 承認通知書
    const FILE_KIND_GUARANTEE_CONTRACT = 29;                   // 保証委託契約書
    const FILE_KIND_FIRE_INSURANCE_GUIDE = 30;                 // 火災保険のご案内
    const FILE_KIND_CONTRACT_CONFIRMATION = 31;                // 契約内容確認書
    const FILE_KIND_KEY_RECEIPT = 32;                          // 鍵預かり書
    const FILE_KIND_MOVE_IN_CHECKLIST = 33;                    // 入居時チェックシート
    const FILE_KIND_RESTORE_MEMO = 34;                         // 原復メモ

    protected $table = 'en_progress_files';

    protected $guarded = [
        'id'
    ];
}
