package com.kwcontrol.contractorportal

import android.annotation.SuppressLint
import android.content.Context
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.webkit.CookieManager
import android.webkit.URLUtil
import android.webkit.WebChromeClient
import android.webkit.WebStorage
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.kwcontrol.contractorportal.databinding.ActivityMainBinding

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding

    private val preferences by lazy {
        getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
    }

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        binding.webView.settings.javaScriptEnabled = true
        binding.webView.settings.domStorageEnabled = true
        binding.webView.settings.databaseEnabled = true
        binding.webView.settings.loadsImagesAutomatically = true
        binding.webView.settings.useWideViewPort = true
        binding.webView.settings.loadWithOverviewMode = true
        binding.webView.settings.setSupportZoom(false)

        binding.webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                return false
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                super.onPageFinished(view, url)
                binding.swipeRefresh.isRefreshing = false
                binding.progressBar.visibility = View.GONE
            }
        }

        binding.webView.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                super.onProgressChanged(view, newProgress)
                binding.progressBar.visibility = if (newProgress in 1..99) View.VISIBLE else View.GONE
            }
        }

        binding.swipeRefresh.setOnRefreshListener {
            binding.webView.reload()
        }

        binding.btnOpenPortal.setOnClickListener {
            val rawUrl = binding.edtContractorUrl.text?.toString()?.trim().orEmpty()
            openPortal(rawUrl)
        }

        binding.btnLogout.setOnClickListener {
            logout()
        }

        val savedUrl = preferences.getString(KEY_LAST_URL, "").orEmpty()
        if (savedUrl.isNotBlank()) {
            binding.edtContractorUrl.setText(savedUrl)
            openPortal(savedUrl)
        } else {
            showInputScreen()
        }
    }

    private fun normalizeUrl(url: String): String {
        if (url.isBlank()) return ""
        return if (URLUtil.isNetworkUrl(url)) {
            url
        } else {
            "https://$url"
        }
    }

    private fun isContractorUrl(url: String): Boolean {
        val uri = Uri.parse(url)
        val path = uri.path.orEmpty()
        return path.contains("/contract/") && path.contains("/profile")
    }

    private fun openPortal(inputUrl: String) {
        val normalized = normalizeUrl(inputUrl)
        if (normalized.isBlank()) {
            Toast.makeText(this, "ادخل رابط المتعهد أولاً", Toast.LENGTH_SHORT).show()
            return
        }

        if (!isContractorUrl(normalized)) {
            Toast.makeText(this, "الرابط يجب أن يكون صفحة متعهد: /contract/{token}/profile", Toast.LENGTH_LONG).show()
            return
        }

        preferences.edit().putString(KEY_LAST_URL, normalized).apply()
        showPortalScreen()
        binding.progressBar.visibility = View.VISIBLE
        binding.webView.loadUrl(normalized)
    }

    private fun logout() {
        preferences.edit().remove(KEY_LAST_URL).apply()

        val cookieManager = CookieManager.getInstance()
        cookieManager.removeAllCookies(null)
        cookieManager.flush()

        WebStorage.getInstance().deleteAllData()
        binding.webView.clearHistory()
        binding.webView.clearCache(true)
        binding.webView.loadUrl("about:blank")

        showInputScreen()
        Toast.makeText(this, "تم تسجيل الخروج", Toast.LENGTH_SHORT).show()
    }

    private fun showInputScreen() {
        binding.inputContainer.visibility = View.VISIBLE
        binding.portalContainer.visibility = View.GONE
    }

    private fun showPortalScreen() {
        binding.inputContainer.visibility = View.GONE
        binding.portalContainer.visibility = View.VISIBLE
    }

    override fun onBackPressed() {
        if (binding.portalContainer.visibility == View.VISIBLE && binding.webView.canGoBack()) {
            binding.webView.goBack()
            return
        }
        super.onBackPressed()
    }

    companion object {
        private const val PREFS_NAME = "contractor_portal_prefs"
        private const val KEY_LAST_URL = "last_contractor_url"
    }
}
