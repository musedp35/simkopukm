<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\SavingsInterest;
use App\Models\SimpananProduct;
use App\Models\LoanInterest;
use App\Models\LoanProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConstantsController extends BaseAdminController
{
    public function index(): View
    {
        return view('admin.constants.index');
    }

    public function simhar(): View
    {
        $interests = SavingsInterest::where('SAVINGS_PRODUCT_ID', 2)
            ->where('ACTIVE_STATUS', 1)
            ->orderBy('SAVINGS_INTEREST_NAME')
            ->get();
        $product = SimpananProduct::where('SAVINGS_PRODUCT_ID', 2)
            ->where('ACTIVE_STATUS_ID', 1)
            ->first();
        $supports = $this->buildSavingsSupports($product, 2);
        return view('admin.constants.simhar', compact('interests', 'supports'));
    }

    public function ssb(): View
    {
        $interests = SavingsInterest::where('SAVINGS_PRODUCT_ID', 3)
            ->where('ACTIVE_STATUS', 1)
            ->orderBy('SAVINGS_INTEREST_NAME')
            ->get();
        $product = SimpananProduct::where('SAVINGS_PRODUCT_ID', 3)
            ->where('ACTIVE_STATUS_ID', 1)
            ->first();
        $supports = $this->buildSavingsSupports($product, 3);
        return view('admin.constants.ssb', compact('interests', 'supports'));
    }

    public function pinjaman(): View
    {
        $interests = LoanInterest::where('LOAN_PRODUCT_ID', 4)
            ->where('ACTIVE_status', 1)
            ->orderBy('LOAN_INTEREST_NAME')
            ->get();
        $product = LoanProduct::where('LOAN_PRODUCT_ID', 4)
            ->where('ACTIVE_PRODUCT', 1)
            ->first();
        $supports = $this->buildLoanSupports($product, 4);
        return view('admin.constants.pinjaman', compact('interests', 'supports'));
    }

    private function buildSavingsSupports($product, int $productId): array
    {
        $supports = [];
        if (!$product) return $supports;
        $fields = [
            ['Min. Saldo Endapan', $product->MIN_DAILY_BALANCE],
            ['Min. Setor', $product->MIN_DAILY_DEBIT],
            ['Min. Tarik', $product->MIN_DAILY_CREDIT],
        ];
        foreach ($fields as $f) {
            if ($f[1] !== null && $f[1] !== '') {
                $supports = array_merge($supports, $this->explodeCustom($f[0], $f[1], $product->LOGTIME, $productId));
            }
        }
        if ($product->CUSTOM_NAME !== null && $product->CUSTOM_NAME !== '') {
            $supports = array_merge($supports, $this->explodeCustom($product->CUSTOM_NAME, $product->CUSTOM_VALUE, $product->LOGTIME, $productId));
        }
        return $supports;
    }

    private function buildLoanSupports($product, int $productId): array
    {
        $supports = [];
        if (!$product) return $supports;
        $fields = [
            ['Max. Bulan Pinjaman', $product->MAX_MONTH_CREDIT],
            ['Max. Limit Pinjaman', $product->MAX_LIMIT_CREDIT],
            ['Max. Nilai Wajib Beli', $product->MAX_MANDATORY_SHOPPING],
        ];
        foreach ($fields as $f) {
            if ($f[1] !== null && $f[1] !== '') {
                $supports = array_merge($supports, $this->explodeCustom($f[0], $f[1], $product->LOGTIME, $productId));
            }
        }
        if ($product->CUSTOM_NAME !== null && $product->CUSTOM_NAME !== '') {
            $supports = array_merge($supports, $this->explodeCustom($product->CUSTOM_NAME, $product->CUSTOM_VALUE, $product->LOGTIME, $productId));
        }
        return $supports;
    }

    private function explodeCustom(string $name, string $value, string $logtime, int $id): array
    {
        if (strpos($name, '|') !== false && strpos($value, '|') !== false) {
            $names = explode('|', $name);
            $vals  = explode('|', $value);
            $items = [];
            foreach ($names as $i => $n) {
                $items[] = ['name' => trim($n), 'value' => trim($vals[$i] ?? ''), 'logtime' => $logtime, 'id' => $id, 'type' => 'custom'];
            }
            return $items;
        }
        return [['name' => trim($name), 'value' => trim($value), 'logtime' => $logtime, 'id' => $id, 'type' => 'normal']];
    }

    public function saveSavingsInterest(Request $request): RedirectResponse
    {
        $request->validate([
            'SAVINGS_INTEREST_NAME' => 'required|max:100',
            'SAVINGS_INTEREST_RATE' => 'required|numeric|min:0',
            'SAVINGS_PRODUCT_ID'    => 'required|in:2,3',
        ]);
        $data = [
            'SAVINGS_INTEREST_NAME'    => $request->input('SAVINGS_INTEREST_NAME'),
            'SAVINGS_INTEREST_RATE'    => $request->input('SAVINGS_INTEREST_RATE'),
            'SAVINGS_PRODUCT_ID'       => $request->input('SAVINGS_PRODUCT_ID'),
            'ACTIVE_STATUS'            => $request->input('ACTIVE_STATUS', 1),
            'MEMBER_STATUS_ID'         => $request->input('MEMBER_STATUS_ID'),
            'TAX_EXPENSE_RATE'         => $request->input('TAX_EXPENSE_RATE', 0),
            'INTEREST_EXPENSE_NOMINAL' => $request->input('INTEREST_EXPENSE_NOMINAL', 0),
            'LOGTIME'                  => now()->format('Y-m-d H:i:s'),
        ];
        if ($request->filled('SAVINGS_INTEREST_ID')) {
            SavingsInterest::where('SAVINGS_INTEREST_ID', $request->input('SAVINGS_INTEREST_ID'))->update($data);
            $msg = 'Konstanta simpanan berhasil diperbarui.';
        } else {
            SavingsInterest::create($data + ['SAVINGS_INTEREST_ID' => strtoupper(substr(md5(uniqid()), 0, 4))]);
            $msg = 'Konstanta simpanan berhasil ditambahkan.';
        }
        session()->flash('alert-success', $msg);
        return redirect()->route('admin.constants.index');
    }

    public function deleteSavingsInterest(int $id): RedirectResponse
    {
        SavingsInterest::where('SAVINGS_INTEREST_ID', $id)->update([
            'ACTIVE_STATUS' => 0,
            'LOGTIME'       => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Konstanta simpanan dinonaktifkan.');
        return redirect()->route('admin.constants.index');
    }

    public function saveSimpananProduct(Request $request): RedirectResponse
    {
        $request->validate(['SAVINGS_PRODUCT_ID' => 'required|in:2,3']);
        SimpananProduct::where('SAVINGS_PRODUCT_ID', $request->input('SAVINGS_PRODUCT_ID'))->update([
            'MIN_DAILY_BALANCE' => $request->input('MIN_DAILY_BALANCE'),
            'MIN_DAILY_DEBIT'   => $request->input('MIN_DAILY_DEBIT'),
            'MIN_DAILY_CREDIT'  => $request->input('MIN_DAILY_CREDIT'),
            'CUSTOM_NAME'       => $request->input('CUSTOM_NAME'),
            'CUSTOM_VALUE'      => $request->input('CUSTOM_VALUE'),
            'LOGTIME'           => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Parameter produk simpanan berhasil disimpan.');
        return redirect()->route('admin.constants.index');
    }

    public function saveLoanInterest(Request $request): RedirectResponse
    {
        $request->validate([
            'LOAN_INTEREST_NAME' => 'required|max:100',
            'LOAN_INTEREST_RATE' => 'required|numeric|min:0',
        ]);
        $data = [
            'LOAN_INTEREST_NAME' => $request->input('LOAN_INTEREST_NAME'),
            'LOAN_INTEREST_RATE' => $request->input('LOAN_INTEREST_RATE'),
            'LOAN_PRODUCT_ID'    => $request->input('LOAN_PRODUCT_ID', 4),
            'ACTIVE_status'      => $request->input('ACTIVE_status', 1),
            'LOGTIME'            => now()->format('Y-m-d H:i:s'),
        ];
        if ($request->filled('LOAN_INTEREST_ID')) {
            LoanInterest::where('LOAN_INTEREST_ID', $request->input('LOAN_INTEREST_ID'))->update($data);
            $msg = 'Konstanta pinjaman berhasil diperbarui.';
        } else {
            LoanInterest::create($data + ['LOAN_INTEREST_ID' => strtoupper(substr(md5(uniqid()), 0, 4))]);
            $msg = 'Konstanta pinjaman berhasil ditambahkan.';
        }
        session()->flash('alert-success', $msg);
        return redirect()->route('admin.constants.index');
    }

    public function deleteLoanInterest(int $id): RedirectResponse
    {
        LoanInterest::where('LOAN_INTEREST_ID', $id)->update([
            'ACTIVE_status' => 0,
            'LOGTIME'       => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Konstanta pinjaman dinonaktifkan.');
        return redirect()->route('admin.constants.index');
    }

    public function saveLoanProduct(Request $request): RedirectResponse
    {
        $request->validate(['LOAN_PRODUCT_ID' => 'required']);
        LoanProduct::where('LOAN_PRODUCT_ID', $request->input('LOAN_PRODUCT_ID'))->update([
            'MAX_MONTH_CREDIT'     => $request->input('MAX_MONTH_CREDIT'),
            'MAX_LIMIT_CREDIT'     => $request->input('MAX_LIMIT_CREDIT'),
            'MAX_MANDATORY_SHOPPING' => $request->input('MAX_MANDATORY_SHOPPING'),
            'CUSTOM_NAME'          => $request->input('CUSTOM_NAME'),
            'CUSTOM_VALUE'         => $request->input('CUSTOM_VALUE'),
            'LOGTIME'              => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Parameter produk pinjaman berhasil disimpan.');
        return redirect()->route('admin.constants.index');
    }
}
